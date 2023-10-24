<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Provider;
use App\V2\Provider\Policy\Payment\PaymentPolicy;
use App\V2\Provider\Policy\Test\TestPolicy;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ProviderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', null, [
                'label' => 'Название'
            ])
            ->add('slug', null, [
                'label' => 'Slug'
            ])
            ->add('payment_policy', EnumType::class, [
                'class' => PaymentPolicy::class,
                'label' => 'Политика оплаты',
                'choice_label' => fn($choice) => match ($choice) {
                    PaymentPolicy::PRE => 'Предоплата',
                    PaymentPolicy::POST => 'Постоплата',
                },
                'help' => 'Предоплата означает предел количества пользователей компании',
            ])
            ->add('access_limit', IntegerType::class, [
                'label' => 'Предел количества пользователей компании (при выбранной политике предоплаты)',
            ])
            ->add('test_policy', EnumType::class, [
                'class' => TestPolicy::class,
                'label' => 'Политика тестов',
                'choice_label' => fn($choice) => match ($choice) {
                    TestPolicy::ONE_PROFTEST => 'Профтест',
                    TestPolicy::ONE_PROFTEST_ONE_BONUS => 'Профтест + Бонус',
                    TestPolicy::UNLIMITED_PROFTEST => 'Безлимитка',
                },
                'help' => 'Какие тесты доступны пользователям компании',
            ])
            ->add('save', SubmitType::class, [
                'row_attr' => ['class' => 'mt-4']]);

        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
            $form = $event->getForm();
            /**@var Provider $provider */
            if (null === $provider = $event->getData()) {
                return;
            }

            if ($provider->getPaymentPolicy() === PaymentPolicy::PRE && $provider->getAccessLimit() <= 0) {
                $form->addError(new FormError('Необходимо указать предел количества пользователей, поскольку установлена политика предоплаты.'));
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Provider::class,
        ]);
    }
}