<?php

/*
 * This file is part of the EasyAdminBundle.
 *
 * (c) Javier Eguiluz <javier.eguiluz@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace EasyCorp\Bundle\EasyAdminBundle\Form\Extension;

use EasyCorp\Bundle\EasyAdminBundle\Form\Util\LegacyFormHelper;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Extension that injects EasyAdmin related information in the view used to
 * render the form.
 *
 * @author Maxime Steinhausser <maxime.steinhausser@gmail.com>
 */
class EasyAdminExtension extends AbstractTypeExtension
{
    /** @var RequestStack|null */
    private $requestStack;

    /**
     * @param RequestStack|null $requestStack
     */
    public function __construct(RequestStack $requestStack = null)
    {
        $this->requestStack = $requestStack;
    }

    /**
     * {@inheritdoc}
     */
    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        $request = null;
        if (null !== $this->requestStack) {
            $request = $this->requestStack->getCurrentRequest();
        }

        if (null === $request) {
            return;
        }

        if ($request->attributes->has('easyadmin')) {
            $easyadmin = $request->attributes->get('easyadmin');
            $entity = $easyadmin['entity'];
            $action = $easyadmin['view'];
            $fields = isset($entity[$action]['fields']) ? $entity[$action]['fields'] : array();
            $view->vars['easyadmin'] = array(
                'entity' => $entity,
                'view' => $action,
                'item' => $easyadmin['item'],
                'field' => isset($fields[$view->vars['name']]) ? $fields[$view->vars['name']] : null,
                'form_group' => $form->getConfig()->getAttribute('easyadmin_form_group'),
                'form_tab' => $form->getConfig()->getAttribute('easyadmin_form_tab'),
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return LegacyFormHelper::getType('form');
    }
}
