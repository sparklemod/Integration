<?php

namespace App\models;

use AmoCRM\Collections\ContactsCollection;
use AmoCRM\Collections\CustomFieldsValuesCollection;
use AmoCRM\Collections\Leads\LeadsCollection;
use AmoCRM\Collections\LinksCollection;
use AmoCRM\Models\ContactModel;
use AmoCRM\Models\CustomFieldsValues\MultitextCustomFieldValuesModel;
use AmoCRM\Models\CustomFieldsValues\ValueCollections\MultitextCustomFieldValueCollection;
use AmoCRM\Models\CustomFieldsValues\ValueModels\MultitextCustomFieldValueModel;

class LeadModel
{
    private \AmoCRM\Client\AmoCRMApiClient $apiClient;
    public function __construct($apiClient)
    {
        $this->apiClient = $apiClient;
    }

    public function addLead(array $data): bool
    {
        $leadsService = $this->apiClient->leads();

        $lead = new \AmoCRM\Models\LeadModel();
        $lead->setPrice($data['price']);

        $leadsCollection = new LeadsCollection();
        $leadsCollection->add($lead);
        $leadsCollection = $leadsService->add($leadsCollection);

        $contact = new ContactModel();
        $contact->setName($data['name']);
        $contactModel = $this->apiClient->contacts()->addOne($contact);

        $customFields = new CustomFieldsValuesCollection();
        $this->addCustomField($customFields, 'PHONE', $data['phone']);
        $this->addCustomField($customFields, 'EMAIL', $data['email']);
        $contact->setCustomFieldsValues($customFields);

        $contactsCollection = new ContactsCollection();
        $contactsCollection->add($contact);
        $contactsCollection = $this->apiClient->contacts()->add($contactsCollection);

        $lead = $this->apiClient->leads()->getOne($lead->getId());

        $links = new LinksCollection();
        $links->add($lead);
        $this->apiClient->contacts()->link($contactModel, $links);

        return true;
    }


    private function addCustomField(CustomFieldsValuesCollection $customFields, string $fieldCode, string $value): void
    {
        $field = $customFields->getBy('fieldCode', $fieldCode);

        if (empty($field)) {
            $field = (new MultitextCustomFieldValuesModel())->setFieldCode($fieldCode);
            $customFields->add($field);
        }

        $field->setValues(
            (new MultitextCustomFieldValueCollection())
                ->add(
                    (new MultitextCustomFieldValueModel())
                        ->setEnum('WORK')
                        ->setValue($value)
                )
        );
    }
}