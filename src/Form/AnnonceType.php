<?php

namespace App\Form;

use App\Entity\Ad;
use App\Form\ApplicationType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Url;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class AnnonceType extends ApplicationType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title',TextType::class, $this->getConfiguration('Titre', 'Entrer votre titre.'))
            ->add('slug', TextType::class, $this->getConfiguration('Alias','Alias de l\'annonce',['required'=> false]))
            ->add('introduction', TextType::class, $this->getConfiguration('Résumé','Présentation de votre bien.'))
            ->add('content', TextareaType::class, $this->getConfiguration('Description détaillé','décriver les prestations'))
            ->add('rooms', IntegerType::class, $this->getConfiguration('Nombre de chambres','Nombre de chambres'))
            ->add('price', MoneyType::class, $this->getConfiguration('Prix','Prix par nuit'))
            ->add('coverImage',UrlType::class, $this->getConfiguration('Image de couverture','Insérer une image'))
            //->add('coverImage',FileType::class, $this->getConfiguration('Image de couverture','Insérer une image'))
            ->add('images', CollectionType::class,['entry_type' =>ImageType::class,'allow_add'=> true,'allow_delete'=> true])
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Ad::class,
        ]);
    }
}
