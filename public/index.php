<?hh // strict
echo "Hello World!";

echo "New";

function enclosePerson($name) {
  return function ($doCommand) use ($name) {
    return sprintf('%s, %s', $name, $doCommand);
  };
}

// Enclose "Clay" string into closure
$clay = enclosePerson('Clay');

// Invoke closure with command 
echo $clay('get me sweet tea!');
