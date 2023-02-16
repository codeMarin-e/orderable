<?php
    namespace App\Traits;

    use Illuminate\Support\Facades\DB;

    trait rderable {

        public static function bootOrderable() {
            static::creating( static::class.'@onCreating_orderable' );
            static::deleting( static::class.'@onDeleted_orderable' );
        }

        public static function freeOrd($qryBld = null) {
            $raw = DB::raw("MAX(".static::getOrdField().') as freeOrd');
            $qryBld = $qryBld? clone $qryBld : static::getModel();
            return (int)$qryBld->select($raw)->first()->freeOrd+1;
        }

        public static function getOrdField() {
            return static::$addonStatics['ordField']?? 'ord';
        }

        public static function orderList($orderList = array()) {
            if(empty($orderList)) return;
            $fakeModel = static::getModel();
            $table = $fakeModel->getTable();
            $primaryKey = $fakeModel->getKeyName();
            $orderField = static::getOrdField();
            $prepares = [];
            $statement = "UPDATE {$table} SET {$table}.{$orderField} = (CASE {$table}.{$primaryKey}";
            foreach($orderList as $newOrd => $objId) {
                $statement .= " WHEN ? THEN ?";
                $prepares[] = $objId;
                $prepares[] = $newOrd;
            }
            $objIds = array_values($orderList);
            $whereInStm = implode(', ', array_fill(0, count($objIds), '?'));
            $statement .= " END), {$table}.updated_at = ?";
            $statement .= " WHERE {$table}.{$primaryKey} IN ({$whereInStm})";
            $prepares = array_merge($prepares, [ new \Datetime() ], $objIds);
            return DB::statement( DB::raw($statement), $prepares);
        }

        public function getPrevious($qryBld = null) {
            if(method_exists($this, 'orderableQryBld') || static::hasMacro('orderableQryBld')){
                $qryBld = $this->orderableQryBld($qryBld);
            }
            $qryBld = $qryBld? clone $qryBld : $this;
            $orderField = static::getOrdField();
            return $qryBld
                ->where($orderField, '<', $this->{$orderField})
                ->orderBy($orderField, 'DESC')
                ->first();
        }

        public function getNext($qryBld = null) {
            if(method_exists($this, 'orderableQryBld') || static::hasMacro('orderableQryBld')){
                $qryBld = $this->orderableQryBld($qryBld);
            }
            $qryBld = $qryBld? clone $qryBld : $this;
            $orderField = static::getOrdField();
            return $qryBld
                ->where($orderField, '>', $this->{$orderField})
                ->orderBy($orderField, 'ASC')
                ->first();
        }

        public function orderMove($direction, $qryBld = null) {
            $other = $direction == 'up'? $this->getPrevious($qryBld) : $this->getNext($qryBld);
            if(!$other) return;
            static::orderList([
                $other->ord => $this->id,
                $this->ord => $other->id
            ]);
            $this->touch();
            $other->touch();
        }

        public function orderMe($qryBld = null) {
            if(method_exists($this, 'orderableQryBld') || static::hasMacro('orderableQryBld') ){
                $qryBld = $this->orderableQryBld($qryBld);
            }
            $qryBld = $qryBld? clone $qryBld : $this;
            $this->{static::getOrdField()} = static::freeOrd($qryBld);
            return $qryBld->save();
        }

        public function onCreating_orderable($model) {
            if(method_exists($model, 'orderableQryBld') || static::hasMacro('orderableQryBld') ){
                $qryBld = $model->orderableQryBld();
            }
            $qryBld = isset($qryBld)? $qryBld : $model;
            $ordField = static::getOrdField();
            $model->{$ordField} = static::freeOrd($qryBld);
        }

        public function onDeleted_orderable($model) {
            if(method_exists($model, 'orderableQryBld') || static::hasMacro('orderableQryBld') ){
                $qryBld = $model->orderableQryBld();
            }
            $qryBld = isset($qryBld)? $qryBld : $model;
            $ordField = static::getOrdField();
            $qryBld->where($ordField, '>', $model->{$ordField})
                ->update([
                    $ordField => DB::raw("{$ordField}-1"),
                    'updated_at' => new \Datetime()
                ]);
        }


    }
