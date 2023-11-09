<?php

namespace Drupal\utility\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;


class SearchForm extends FormBase {

    public function getFormId(){
        return 'search_form';
    }

    public function buildForm(array $form,FormStateInterface $form_state ) {
        $form_state->setMethod('GET');

        $first_name = \Drupal::request()->query->get('first_name');
        $gender = \Drupal::request()->query->get('gender');
        $age = \Drupal::request()->query->get('age');

        $form['first_name'] = array(
            '#type' => 'textfield',
            '#title' => t('First Name:'),
            '#default_value'=> $first_name,
            //'#required' => TRUE,
        );

        $form['gender'] = array(
            '#type' => 'select',
            '#title' => t('Gender:'),
            '#options'=>[''=>'Select','M'=>'M','F'=>'F','other'=>'Other'],
            '#default_value'=>$gender,
            //'#required' => TRUE,
        );
        $form['age'] = array(
            '#type' => 'select',
            '#title' => t('Age:'),
            '#options'=>[''=>'Select','0-25'=>'0-25','25-40'=>'25-40','40-60'=>'40-60','>60'=>'above 60'],
            '#default_value'=>$age,
            //'#required' => TRUE,
        );

        $form['actions']['submit'] = array(
        '#type' => 'submit',
        '#value' => $this->t('Search'),
        '#button_type' => 'primary',
        '#name' => '',
        );
        
        $form['reset'] = array(
            '#type' => 'markup',
            '#markup' => '<input class="form-submit" type="reset" value="Reset">',
          );

        $form['export'] = [
            '#type' => 'markup',
            '#markup' => '<a href="/export?first_name='.$first_name.'&gender='.$gender.'&age='.$age.'" target="_blank">Export</a>',
            //'#weight' => '3',

        ];
            return $form;
    }

   

       

    public function submitForm(array &$form, FormStateInterface $form_state) {
        // Processing form data.
        
      }
    
}
