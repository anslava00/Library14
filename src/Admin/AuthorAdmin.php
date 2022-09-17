<?php
// src/Admin/AuthorAdmin.php

namespace App\Admin;

use Doctrine\DBAL\Types\TimeType;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

final class AuthorAdmin extends AbstractAdmin
{
    protected function configureFormFields(FormMapper $form): void
    {
        $form
            ->add('name', TextType::class, [
                'label' => 'ФИО',
            ])
            ->add('dateOfBirth', DateType::class, [
                'required' => false,
                'label' => 'Дата рождения',
            ])
            ->add('phone', NumberType::class, [
                'required' => false,
                'label' => 'Телефон',
            ])
            ->add('email', EmailType::class, [
                'label' => 'Почта',
            ]);

    }

    protected function configureDatagridFilters(DatagridMapper $datagrid): void
    {
        $datagrid
            ->add('name')
            ->add('dateOfBirth')
            ->add('phone')
            ->add('email');
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->add('name', TextType::class)
            ->add('dateOfBirth','date',[
                'date_format' => 'yyyy-MM-dd'
            ])
            ->add('phone', NumberType::class)
            ->add('email', EmailType::class);
    }

    protected function configureShowFields(ShowMapper $show): void
    {
        $show
            ->add('name')
            ->add('dateOfBirth')
            ->add('phone')
            ->add('email');
    }
}