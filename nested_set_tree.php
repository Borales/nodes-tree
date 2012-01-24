<?php

class Comment
{
    public $id;
    public $parent;
    public $children;
    public $text = 'some comment text';
    
    
    /**
     * Faster
     */
    public static function buildTree( $dataset, $parent_field = 'parent', $children_field = 'children')
    {
        $tree = array();
        foreach ($dataset as $id=>&$node) {
            if ($node->{$parent_field} == null) {
    			$tree[$id] = &$node;
    		} else {
    			if ( !isset($dataset[$node->{$parent_field}]->{$children_field}) ) 
                    $dataset[$node->{$parent_field}]->{$children_field} = array();
                    
    			$dataset[$node->{$parent_field}]->{$children_field}[$id] = &$node;
    		}
    	}
    
    	return $tree;
    }
    
    
    /**
     * Slower
     */
    public static function buildRecursiveTree( $dataset, $parent = null, $parent_field = 'parent', $children_field = 'children' )
    {
        $tree = array();
    	foreach ($dataset as $id=>$node) {
    		if ($node->{$parent_field} !== $parent) continue;
    		$node->{$children_field} = self::buildRecursiveTree($dataset, $id);
    		$tree[$id] = $node;
    	}
    
    	return $tree;
    }
}

// -----------------------------------------------------------------------------
// -----------------------------------------------------------------------------
// -----------------------------------------------------------------------------

$comms = array();

for( $j = 1; $j < 100; $j++ )
{
    $max = !empty($comms) ? rand(1, max(array_keys($comms))) : null;
    $rangeSelect = array(null, $max, $max);
    
    $_comm = new Comment();
    $_comm->id = $j;
    $_comm->parent = $rangeSelect[array_rand($rangeSelect)];
    
    $comms[$_comm->id] = $_comm;
}

// -----------------------------------------------------------------------------

$before_build = microtime(true);
for($i = 0; $i < 100; $i++)
{
    $comments_tree = Comment::buildTree($comms);
}
$after_build = microtime(true) - $before_build;

// -----------------------------------------------------------------------------

$before_recursive_build = microtime(true);
for($i = 0; $i < 100; $i++)
{
    $comments_tree = Comment::buildRecursiveTree($comms);
}
$after_recursive_build = microtime(true) - $before_recursive_build;

// -----------------------------------------------------------------------------

echo "<pre>";

print_r( 
    array(
        'regular' => $after_build, 
        'recursive' => $after_recursive_build
    ) 
);

print_r(
    $comments_tree
);

?>