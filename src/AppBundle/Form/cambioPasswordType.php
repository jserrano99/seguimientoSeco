<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class cambioPasswordType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('new', PasswordType::class, array(
                                    "label"=>'Nueva Contraseña',
                                    "required" => 'required',
                                    'attr' => array('class' => 'form-new form-control'
                )))
                ->add('new2', PasswordType::class, array(
                                    "label"=>'Repita Contraseña',
                                    "required" => 'required',
                                    'attr' => array('class' => 'form-new2 form-control'
                )))
                ->add('Cambiar', SubmitType::class, array(
                                    "attr" => array("class" => "form-submit btn btn-success"
                )))
                ;
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)  {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\cambioPassword'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'comunbundle_cambiopassword';
    }


}
