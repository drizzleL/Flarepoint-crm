<?php
namespace App\Repositories\Client;

use App\Models\Client;
use App\Models\Industry;
use App\Models\Invoices;
use App\Repositories\BaseRepository;

class ClientRepository extends BaseRepository implements ClientRepositoryContract
{
    const CREATED = 'created';
    const UPDATED_ASSIGN = 'updated_assign';

    public function __construct(Client $client)
    {
        $this->model = $client;
    }

    public function find($id)
    {
        $model = $this->cloneModel();
        return $model->findOrFail($id);
    }
    public function listAllClients()
    {
        $model = $this->cloneModel();
        return $model->pluck('name', 'id');
    }

    public function getInvoices($id)
    {
        $model = $this->cloneModel();
        $invoice = $model->findOrFail($id)->invoices()->with('tasktime')->get();

        return $invoice;
    }

    public function getAllClientsCount()
    {
        $model = $this->cloneModel();
        return $model->count();
    }

    public function listAllIndustries()
    {
        return Industry::pluck('name', 'id');
    }

    public function create($requestData)
    {
        $client = $this->model->create($requestData);
        Session()->flash('flash_message', 'Client successfully added');
        event(new \App\Events\ClientAction($client, self::CREATED));
        return $client;
    }

    public function assignTenant($model, $id)
    {
        $model->tenant_id = $id;
        $model->save();
    }

    public function update($id, $requestData)
    {
        $model = $this->cloneModel();
        $client = $model->findOrFail($id);
        $client->fill($requestData->all())->save();
    }

    public function destroy($id)
    {
        $model = $this->cloneModel();
        try {
            $client = $model->findOrFail($id);
            $client->delete();
            Session()->flash('flash_message', 'Client successfully deleted');
        } catch (\Illuminate\Database\QueryException $e) {
            Session()->flash('flash_message_warning', 'Client can NOT have, leads, or tasks assigned when deleted');
        }
    }
    public function vat($requestData)
    {
        $vat = $requestData->input('vat');

        $country = $requestData->input('country');
        $company_name = $requestData->input('company_name');

        // Strip all other characters than numbers
        $vat = preg_replace('/[^0-9]/', '', $vat);

        function cvrApi($vat)
        {
            if (empty($vat)) {
                // Print error message
                return('Please insert VAT');
            } else {
                // Start cURL
                $ch = curl_init();

                // Set cURL options
                curl_setopt($ch, CURLOPT_URL, 'http://cvrapi.dk/api?search=' . $vat . '&country=dk');
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_USERAGENT, 'Flashpoint');

                // Parse result
                $result = curl_exec($ch);

                // Close connection when done
                curl_close($ch);

                // Return our decoded result
                return json_decode($result, 1);
            }
        }
        $result = cvrApi($vat, 'dk');

        return $result;
    }
}
