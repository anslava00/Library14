<?php
// src/Admin/BookAdmin.php

namespace App\Admin;

use App\Entity\Author;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\Date;

final class BookAdmin extends AbstractAdmin
{
    protected function configureFormFields(FormMapper $form): void
    {
        $form
            ->add('title', TextType::class, [
                'label' => 'Название книги',
            ])
            ->add('description', TextareaType::class, [
                'required' => false,
                'label' => 'Описание',
            ])
            ->add('setFile', FileType::class, [
                'required' => false,
                'data_class' => null,
                'label' => 'Обложка',
            ])
            ->add('year', DateType::class, [
                'required' => false,
                'label' => 'Год выпуска',
            ])
            ->add('authors', EntityType::class, [
                'required' => false,
                'class' => Author::class,
                'choice_label' => 'name',
                'multiple' => true,
                'label' => 'Авторы',
                ]);
    }

    protected function configureDatagridFilters(DatagridMapper $datagrid): void
    {
        $datagrid
            ->add('title')
            ->add('description')
            ->add('year');
//            ->add('authors');
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->add('title', TextType::class,[
                'label' => 'Название книги'
            ])
            ->add('description', TextareaType::class,[
                'label' => 'Описание'
            ])
            ->add('year','date',[
                'date_format' => 'yyyy-MM-dd'
            ])
            ->add('authors', EntityType::class, [
                'associated_property' => 'name',
                'label' => 'Авторы'
            ]);
    }

    protected function configureShowFields(ShowMapper $show): void
    {
        $show->add('title');
    }
}