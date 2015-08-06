<?php
/**
 * Created by TS.
 * User: ts
 * Date: 6/08/15
 * Time: 08:37 AM
 */

namespace APP\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * TODO: mejorar hidratacion de selects para que sean validos para el formulario.
 * Class CotizacionType
 * @package APP\Form\Type
 */
class CotizacionType extends AbstractType
{
    /**
     * @var array
     */
    private $datos;

    /**
     * @param $datosIniciales
     */
    function __construct($datosIniciales)
    {
        $this->datos = $datosIniciales;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('curso', 'choice', [
                'choices' => $this->datos['curso'],
                'placeholder' => '[ Seleccione ]'
            ])
            ->add('pais', 'choice', [
                'label' => 'País',
                'placeholder' => '[ Seleccione ]'
            ])
            ->add('ciudad', 'choice', [
                'placeholder' => '[ Seleccione ]'
            ])
            ->add('centro', 'choice', [
                'placeholder' => '[ Seleccione ]'
            ])
            ->add('semanas', 'choice', [
                'label' => 'Semanas curso',
                'placeholder' => '[ Seleccione ]'
            ])
            ->add('lecciones_por_semana', 'choice', [
                'label' => 'Lecciones por semana',
                'placeholder' => '[ Seleccione ]'
            ])
            ->add('jornada_de_lecciones', 'choice', [
                'label' => 'Jornada lecciones',
                'placeholder' => '[ Seleccione ]'
            ])
            ->add('alojamiento', 'choice', [
                'choices' => ['SI'=>'Si','NO'=>'No'],
                'placeholder' => '[ Seleccione ]'
            ])
            ->add('semanas_de_alojamiento', 'choice', [
                'choices' => range(1,52),
                'label' => '',
                'placeholder' => '[ Seleccione ]'
            ])
            ->add('tipo_de_alojamiento', 'choice', [
                'label' => 'Tipo alojamiento',
                'placeholder' => '[ Seleccione ]'
            ])
            ->add('tipo_de_habitacion', 'choice', [
                'label' => 'Tipo habitación',
                'placeholder' => '[ Seleccione ]'
            ])
            ->add('alimentacion', 'choice', [
                'label' => 'Tipo alimentación',
                'placeholder' => '[ Seleccione ]'
            ])
            ->add('traslado', 'choice', [
                'choices' => ['SI'=>'Si','NO'=>'No'],
                'placeholder' => '[ Seleccione ]'
            ])
            ->add('moneda', 'choice', [
                'choices' => $this->datos['moneda'],
                'placeholder' => '[ Seleccione ]'
            ]);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'form';
    }
}