<?php

namespace Drupal\utility\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Render\Markup;
use Drupal\utility\Form\SearchForm;

class DashboardController extends ControllerBase {

    public function dashboardPage() 
    {
       $header = [
               'sn'=>[
                'data'=>t('S No')
               ],
                'first_name' => [
                    'data'=>t('First Name'),
                    'field'     => 'title',
                  //  'specifier' => 'title',
                ],
                'last_name' => [
                    'data'=>t('Last Name'),
                    'field'     => 'field_last_name',
                   // 'specifier' => 'field_last_name',
                ],
                'gender' => [
                    'data'=>t('Gender'),
                    'field'     => 'field_gender',
                  //  'specifier' => 'field_gender',
                ],
                'age' => [
                    'data'=>t('Age'),
                    'field'     => 'field_age',
                  //  'specifier' => 'field_age',
                ],
                'created' => [
                    'data'=>t('Created'),
                    'field'     => 'created',
                   // 'specifier' => 'created',
                ],
            ];

           // $searchKey = $form_state->getValue('first_name');
        
        $first_name = \Drupal::request()->query->get('first_name');
        $gender = \Drupal::request()->query->get('gender');
        $age = \Drupal::request()->query->get('age');

        $nids = \Drupal::entityQuery('node')
        //db_select('node')
        ->accessCheck(TRUE)
        ->condition('status', 1)
        ->condition('type', 'user_profile');
       
        if(!empty($first_name)){
            $nids =  $nids->condition('title', '%'.$first_name.'%','like');
        }
        if(!empty($gender)){
            $nids =  $nids->condition('field_gender', $gender);
        }
        if(!empty($age)){
            if($age=='>60'){
                $age = str_replace('>','',$age);
                $nids =  $nids->condition('field_age', $age,'>');
            }else{
                $age = explode('-',$age);
                $nids =  $nids->condition('field_age', $age,'BETWEEN');
            }
                              
        }
        
        $nids=$nids->pager(5)
        //->tableSort($header)
        //->limit(5)
        //->orderBy('created')
        ->execute();

        $nodes = \Drupal\node\Entity\Node::loadMultiple($nids);
        
        //print_r($nodes);
        //exit;

        $data=[];
        $i=0;
        foreach($nodes as $node){
            $data[$i]['sn']= ($i+1);
            $data[$i]['first_name']=$node->get('title')->value;
            $data[$i]['last_name']=$node->get('field_last_name')->value;
            $data[$i]['gender']=$node->get('field_gender')->value;
            $data[$i]['age']=$node->get('field_age')->value;
            $data[$i]['created']=date('d-m-Y',$node->get('created')->value); 

           
            $i++;
        }
        //$arg'';
        //$form_state = new \Drupal\Core\Form\FormStateInterface();
        //$myForm = $this->FormBuilder()->getForm('\Drupal\utility\Form\SearchForm');
        //$values=$searchKey;
        $searchform = \Drupal::formBuilder()->getForm('Drupal\utility\Form\SearchForm');
        $build['filter_form'] = [$searchform];
        //$build['#markup'] = ['Download'];
       // $build['searchdata']=[$searchKey];
        $build['table'] = [
        '#type' => 'table',
        '#theme'=>'table',
        '#header' => $header,
        '#rows' => $data,
        '#empty' => t('No content has been found.'),
        ];
        $build['pager'] = [
            '#type' => 'pager',
          ];

        return [
            '#type' => '#markup',
            '#markup' => \Drupal::service('renderer')->render($build)
        ];
    
    }

    public function exportPage(){

        $first_name = \Drupal::request()->query->get('first_name');
        $gender = \Drupal::request()->query->get('gender');
        $age = \Drupal::request()->query->get('age');

        header('Content-Type: text/csv; charset=utf-8');  
        header('Content-Disposition: attachment; filename=data.csv');  
        $output = fopen("php://output", "w");  
        fputcsv($output, array('S No', 'First Name', 'Last Name', 'Gender','Age'));  
        
        $nids = \Drupal::entityQuery('node')
        ->accessCheck(TRUE)
        ->condition('status', 1)
        ->condition('type', 'user_profile');
       
        if(!empty($first_name)){
            $nids =  $nids->condition('title', '%'.$first_name.'%','like');
        }
        if(!empty($gender)){
            $nids =  $nids->condition('field_gender', $gender);
        }
        if(!empty($age)){
            if($age=='>60'){
                $age = str_replace('>','',$age);
                $nids =  $nids->condition('field_age', $age,'>');
            }else{
                $age = explode('-',$age);
                $nids =  $nids->condition('field_age', $age,'BETWEEN');
            }
                              
        }
        
      
        $nids=$nids->execute();

        $nodes = \Drupal\node\Entity\Node::loadMultiple($nids);
        

        $data=[];
        $i=0;
        foreach($nodes as $node){
            $data[$i]['sn']= ($i+1);
            $data[$i]['first_name']=$node->get('title')->value;
            $data[$i]['last_name']=$node->get('field_last_name')->value;
            $data[$i]['gender']=$node->get('field_gender')->value;
            $data[$i]['age']=$node->get('field_age')->value;
            //$data[$i]['created']=date('d-m-Y',$node->get('created')->value); 
           
            fputcsv($output, $data[$i]);

           
            $i++;
        } 
        /*while($row = mysqli_fetch_assoc($result))  
        {  
             fputcsv($output, $row);  
        }  */
        fclose($output);  
        exit;
    }


    function reportPage($node_id){
        $article = \Drupal::entityTypeManager()->getStorage('node')->load($node_id);
        $paragraph_field_items = $article->get('field_faq_section')->referencedEntities();
        $html = '<div class="faq-container">';
        foreach ($paragraph_field_items as $paragraph) {

            // Get the translation
 $paragraph = \Drupal::service('entity.repository')->getTranslationFromContext($paragraph);
              
            $description = [
                '#type' => 'processed_text',
                '#text' => $paragraph->get('field_description')->value,
                '#format' => 'basic_html',
            ];
            $title = $paragraph->get('field_title')->value;
            $description = $paragraph->get('field_description')->value;
            
            $html .= "<div class='title-faq'>$title</div><div class='faq-des'>$description</div>";
          }

        $html .= '</div>';
$mpdf = new \Mpdf\Mpdf(['tempDir' => 'sites/default/files/tmp']); $mpdf->WriteHTML($html);
$mpdf->Output("faq.pdf", "D");
Exit;
    }
}