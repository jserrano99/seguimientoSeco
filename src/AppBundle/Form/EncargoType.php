<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class EncargoType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('codigo', TextType::class, array (
                                    "label" => 'Username',
                                    "required" => 'required',
                                    "attr" => array ("class" => "form-codigo form-control"
                )))
                ->add('nombre', TextType::class, array (
                                    "label" => 'Nombre',
                                    "required" => 'required',
                                    "attr" => array ("class" => "form-nombre form-control"
                )))
                ->add('email', EmailType::class, array(
                                    "label"=>'Correo Electrónico',
                                    "required" => 'required',
                                    'attr' => array('class' => 'form-email form-control'
                )))
                ->add('estadoActual', EntityType::class, array(
                                    'label'=>'EstadoEncargo',
                                    'class' => 'AppBundle:EstadoEncargo',
                                    'required' => "required",
                                    'placeholder' => 'Seleccione EstadoEncargo....',
                                    'attr'=> array("class" => "form-estado form-control")
                ))
                ->add('perfil', ChoiceType::class, array(
                                    "label"=>'Perfil',
                                    "required" => "required",
                                    "choices" => array( "Administrador" => 'ROLE_ADMIN',
                                                        "Encargo" => 'ROLE_USER',
                                                        'Control de Costes' => 'ROLE_COSTES',
                                                        'Gestión Maestros' => 'ROLE_MAESTROS'),
                                   "attr"=> array("class" => "form-perfil form-control"
                )))
                ->add('Guardar', SubmitType::class, array(
                                    "attr" => array("class" => "form-submit btn btn-t btn-success")
                                )
                );
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Encargo'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'comunbundle_usuario';
    }
}
