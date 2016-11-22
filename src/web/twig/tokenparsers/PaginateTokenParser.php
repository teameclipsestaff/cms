<?php
/**
 * @link      https://craftcms.com/
 * @copyright Copyright (c) Pixel & Tonic, Inc.
 * @license   https://craftcms.com/license
 */

namespace craft\web\twig\tokenparsers;

use craft\web\twig\nodes\PaginateNode;
use Twig_Node_Expression_AssignName;
use Twig_Token;
use Twig_TokenParser;

/**
 * Paginates elements via a ElementCriteriaModel instance.
 *
 * @author Pixel & Tonic, Inc. <support@pixelandtonic.com>
 * @since  3.0
 */
class PaginateTokenParser extends Twig_TokenParser
{
    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function parse(Twig_Token $token)
    {
        $lineno = $token->getLine();

        $nodes['criteria'] = $this->parser->getExpressionParser()->parseExpression();
        $this->parser->getStream()->expect('as');
        $targets = $this->parser->getExpressionParser()->parseAssignmentExpression();
        $this->parser->getStream()->expect(Twig_Token::BLOCK_END_TYPE);

        if (count($targets) > 1) {
            $paginateTarget = $targets->getNode(0);
            $nodes['paginateTarget'] = new Twig_Node_Expression_AssignName($paginateTarget->getAttribute('name'), $paginateTarget->getLine());
            $elementsTarget = $targets->getNode(1);
        } else {
            /** @noinspection PhpParamsInspection */
            $nodes['paginateTarget'] = new Twig_Node_Expression_AssignName('paginate', $lineno);
            $elementsTarget = $targets->getNode(0);
        }

        $nodes['elementsTarget'] = new Twig_Node_Expression_AssignName($elementsTarget->getAttribute('name'), $elementsTarget->getLine());

        return new PaginateNode($nodes, [], $lineno, $this->getTag());
    }

    /**
     * @inheritdoc
     */
    public function getTag()
    {
        return 'paginate';
    }
}
