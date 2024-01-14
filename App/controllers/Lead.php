<?php

namespace App\controllers;

use Exception;

class Lead extends BaseController
{
    public function addLead()
    {
        $data = [];

        if (!empty($_POST)) {
            try {
                $this->getToken();

                $LeadModel = new \App\models\LeadModel($this->apiClient);
                $newLead = $LeadModel->addLead($_POST);

                if ($newLead === true) {
                    $data['message'] = 'Сделка успешно добавлена';
                }
            } catch (Exception $e) {
                print_r($e);
            }
        }

        $this->render('add', $data);
    }
}