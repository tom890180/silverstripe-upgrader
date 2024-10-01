<?php

namespace SilverStripe\Upgrader\UpgradeRule\PHP\Visitor;

use PhpParser\Comment;
use PhpParser\Node;

/**
 * PHP upgrade config trait
 */
trait VisitorTrait
{
    /**
     * Check if this node (or any parents) has @skipUpgrade PHPDoc
     *
     * @param Node $node
     * @return bool
     */
    protected function detectSkipUpgrade(Node $node = null)
    {
        if (!$node) {
            return false;
        }
    
        $comments = $node->getAttribute('comments');
        if ($comments) {
            foreach ($comments as $comment) {
                // Check for @skipUpgrade, which affects the whole block
                if (stripos($comment->getText(), '@skipUpgrade') !== false) {
                    return true; // Skip this node and all child nodes
                }
            }
        }
    
        // Recurse to parent node, with isParentNode set to true
        $parent = $node->getAttribute('parent');
        return $this->detectSkipUpgrade($parent);
    }

    /**
     * Check if this node has @skipUpgradeLine PHPDoc
     *
     * @param Node $node
     * @return bool
     */
    protected function detectSkipUpgradeLineOnly(Node $node = null, $ourLine = null)
    {
        if (!$node) {
            return false;
        }

        if ($ourLine === null) {
            $ourLine = $node->getLine();
        }

        $comments = $node->getAttribute('comments');
        if ($comments) {
            foreach ($comments as $comment) {
                // Check for @LineSkipUpgrade, which affects the whole block
                if (stripos($comment->getText(), '@LineSkipUpgrade') !== false) {
                    return true; // Skip this node and all child nodes
                }
            }
        }

        // Recurse to parent node, with isParentNode set to true
        $parent = $node->getAttribute('parent');

        if ($parent && $parent->getLine() == $ourLine) {
            return $this->detectSkipUpgradeLineOnly($parent, $ourLine);
        }

        return false;
    }
}
