# o1-weighted-random
Weighted random item picker, in O(1) time complexity

## Usage
```
$c = new O1WeightedRandom();
$c->add("A", 1);    // Item A with weight 1
$c->add("B", 2);    // Item B with weight 2
$c->add("C", 3);    // Item C with weight 3
$c->add("D", 4);    // Item D with weight 4

echo $c->pick();
/// most probability output D

/// You can add more items 
$c->add("E", 10)
echo $c->pick();
/// Now the most probable output is E
```

You can varify the item is choosen by weight.
```
$result = ['A' => 0, 'B' => 0, 'C' => 0, 'D' => 0];
for ($i = 0; $i < 10000; $i++) {
    $key = $c->pick();
    $result[$key] += 1;
}
print_r($result);
// Array
// (
//     [A] => 10001
//     [B] => 20116
//     [C] => 29976
//     [D] => 39907
// )

```

## About O(1) Time Complexity
The `pick()` is usually O(1) time, but the first call to `pick()` is O(n) time complexity. The library need some time to build the data structure at first picking action.

If you add new item after first `pick()` action, the library need and other O(n) time to rebuild the data structure.

This library is base on the algorithm published here: https://www.keithschwarz.com/darts-dice-coins/

You can learn more details of the algorithm here: https://blog.bruce-hill.com/a-faster-weighted-random-choice

