<?php


namespace App\Service\AmoCrm;


use AmoCRM\Client\AmoCRMApiClient;
use AmoCRM\Collections\ContactsCollection;
use AmoCRM\Collections\CustomFieldsValuesCollection;
use AmoCRM\Exceptions\AmoCRMApiException;
use AmoCRM\Exceptions\AmoCRMMissedTokenException;
use AmoCRM\Exceptions\AmoCRMoAuthApiException;
use AmoCRM\Exceptions\InvalidArgumentException;
use AmoCRM\Filters\ContactsFilter;
use AmoCRM\Helpers\EntityTypesInterface;
use AmoCRM\Models\ContactModel;
use AmoCRM\Models\CustomFieldsValues\MultitextCustomFieldValuesModel;
use AmoCRM\Models\CustomFieldsValues\ValueCollections\MultitextCustomFieldValueCollection;
use AmoCRM\Models\CustomFieldsValues\ValueModels\MultitextCustomFieldValueModel;
use AmoCRM\Models\LeadModel;
use App\DTO\Form\Form;

/**
 * Class FormProcessor
 * @package App\Service\AmoCrm
 */
final class FormProcessor
{
    /**
     * @var AmoCRMApiClient
     */
    private $client;

    /**
     * FormProcessor constructor.
     */
    public function __construct()
    {
        $this->client = ClientFactory::make(true);
    }

    /**
     * @param Form $form
     * @throws AmoCRMApiException
     * @throws AmoCRMMissedTokenException
     * @throws AmoCRMoAuthApiException
     * @throws InvalidArgumentException
     */
    public function process(Form $form): void
    {
        // Try to find existing contact
        $contact = $this->findContact($form);

        // Create new contact if not exists already
        if (!$contact) {
            $contact = $this->createContact($form);
        }

        // Create lead with attached contact
        $this->createLead($form, $contact);
    }

    private function customFieldsMap(Form $form): array
    {
        return [
            'PHONE' => $form->getPhone()->getValue(),
            'EMAIL' => $form->getEmail()->getValue(),
        ];
    }

    /**
     * @param Form $form
     * @return ContactModel|null
     * @throws AmoCRMApiException
     * @throws AmoCRMMissedTokenException
     * @throws AmoCRMoAuthApiException
     * @throws InvalidArgumentException
     */
    private function findContact(Form $form): ?ContactModel
    {
        // Get contact custom fields
        $customFieldsService = $this->client->customFields(EntityTypesInterface::CONTACTS);
        $customFields = $customFieldsService ? $customFieldsService->get() : null;

        // Add contact filters by Phone and Email
        $contactFilters = [];
        if ($customFields) {
            $fields = $this->customFieldsMap($form);
            foreach ($fields as $code => $value) {
                $field = $customFields->getBy('code', $code);

                if ($field) {
                    $contactFilters[$field->getId()] = $value;
                }
            }
        }

        // Get one contact and return it or null
        $filter = new ContactsFilter();
        $filter->setCustomFieldsValues($contactFilters)->setLimit(1);
        $contact = $this->client->contacts()->get($filter);
        return $contact ? $contact->first() : null;
    }

    /**
     * @param Form $form
     * @return ContactModel
     * @throws AmoCRMApiException
     * @throws AmoCRMMissedTokenException
     * @throws AmoCRMoAuthApiException
     */
    private function createContact(Form $form): ContactModel
    {
        $contact = new ContactModel();

        // Populate PHONE and EMAIL fields
        $fields = $this->customFieldsMap($form);
        $contactFields = $contact->getCustomFieldsValues() ?? new CustomFieldsValuesCollection();
        foreach ($fields as $code => $value) {
            // Get field attached to the contact or create a new one
            $field = $contactFields->getBy('fieldCode', $code);
            if ($field === null) {
                $field = (new MultitextCustomFieldValuesModel())->setFieldCode($code);
                $contactFields->add($field);
            }

            // Set value
            $field->setValues(
                (new MultitextCustomFieldValueCollection())
                    ->add(
                        (new MultitextCustomFieldValueModel())
                            ->setEnum('WORK')
                            ->setValue($value)
                    )
            );
        }

        // Update contact with new fields and set the name
        $contact->setCustomFieldsValues($contactFields);
        $contact->setName($form->getName());

        // Save user to get his id
        $contact = $this->client->contacts()->addOne($contact);

        return $contact;
    }

    /**
     * @param Form $form
     * @param ContactModel $contact
     * @return LeadModel
     * @throws AmoCRMApiException
     * @throws AmoCRMMissedTokenException
     * @throws AmoCRMoAuthApiException
     */
    private function createLead(Form $form, ContactModel $contact): LeadModel
    {
        $lead = new LeadModel();
        $lead
            ->setPrice($form->getPrice()->getValue())
            ->setContacts(
                (new ContactsCollection())
                    ->add($contact)
            );

        $lead = $this->client->leads()->addOne($lead);

        return $lead;
    }
}
