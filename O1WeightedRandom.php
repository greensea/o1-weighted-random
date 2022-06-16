<?php
class O1WeightedRandom {
    protected $isSetup = false;

    protected $items = [];
    protected $buckets = [];

    public function add($key, $weight) {
        if ($weight <= 0) {
            throw new Exception("Invalid weight `$weight' for key `$key'");
        }

        $this->items[$key] = [
            'weight' => $weight,
            'key' => $key,
        ];

        $this->isSetup = false;
    }

    public function pick() {
        if (!$this->isSetup) {
            $this->setup();
        }

        $r = mt_rand() / mt_getrandmax();

        $bucket_id = floor($r * count($this->buckets));
        

        if (empty($this->buckets[$bucket_id])) {
            printf("Invalid bucket_id? id=%d, count=%d\n", $bucket_id, count($this->buckets));
        }

        $bucket = $this->buckets[$bucket_id];
        

        $r = mt_rand() / mt_getrandmax();
        if ($r < $bucket['split']) {
            return $bucket['left'];
        } else {
            return $bucket['right'];
        }
    }

    protected function setup() {
        $items = array_merge([], $this->items);
        $sum = 0;
        foreach ($items as $v) {
            $sum += $v['weight'];
        }
        $this->average = $sum / count($items);
        
        $small = [];
        $big = [];
        foreach ($items as $v) {
            if ($v['weight'] < $this->average){
                $small[] = $v;
            } else {
                $big[] = $v;
            }
        }


        $bucket_id = 0;
        $this->buckets = array_fill(0, count($items), []);
        while (!empty($small) || !empty($big)) {
            // printf("before while: small=%d, big=%d. %s\n", count($small), count($big), json_encode([$small, $big]));

            $s = $b = null;
            if (empty($small)) {
                $s = $b = array_pop($big);
            } else if (empty($big)) {
                $s = $b = array_pop($small);
            } else {
                $b = array_pop($big);
                $s = array_pop($small);

                if ($s['weight'] == $b['weight']) {
                    array_push($small, $s);
                    $s = $b;
                }
            }
                        
            // printf("set bucket_id=$bucket_id, big=%f, small=%f\n", $b['weight'], $s['weight']);
            // printf("set bucket_id=$bucket_id, big=%s, small=%s\n", $b['key'], $s['key']);

            $this->buckets[$bucket_id] = [
                'left' => $b['key'],
                'right' => $s['key'],
                'split' => 1 - ($s['weight'] / $this->average),
            ];

            $b['weight'] -= ($this->average - $s['weight']);

            if ($b['key'] == $s['key']) {
                /// Do nothing
            } else if ($b['weight'] == 0) { 
                $b['weight'] = $s['weight'];
                array_push($big, $b);
            } else if ($b['weight'] <= $this->average) {
                array_push($small, $b);
            } else if ($b['weight'] > $this->average) {
                array_push($big, $b);
            } else {
                throw new Exception("Impossiable execution");
            }

            $bucket_id++;

            // printf("after while: small=%f, big=%f\n", count($small), count($big));
            // printf("--------\n");
            
        }

        $this->isSetup = true;
    }

    public function dump() {
        $this->setup();
        print_r($this->buckets);
    }
}

