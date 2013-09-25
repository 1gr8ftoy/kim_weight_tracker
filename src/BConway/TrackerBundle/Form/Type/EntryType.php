<?php
// src/BConway/TrackerBundle/Form/Type/EntryType.php
namespace BConway\TrackerBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class EntryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('entry_date', 'date', array(
                'widget' => 'single_text',
                'label'  => 'Date',
            ))
            ->add('weight', 'number')
            ->add('deficit', 'number')
            ->add('createEntry', 'submit');
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'BConway\TrackerBundle\Entity\Entry',
        ));
    }

    public function getName()
    {
        return 'entry';
    }
}