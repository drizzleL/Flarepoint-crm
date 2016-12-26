<?php
namespace App\Repositories\Lead;

use App\Models\Leads;
use Notifynder;
use Carbon;
use App\Models\Activity;
use DB;
use App\Repositories\BaseRepository;

class LeadRepository extends BaseRepository implements LeadRepositoryContract
{

    const CREATED = 'created';
    const UPDATED_STATUS = 'updated_status';
    const UPDATED_DEADLINE = 'updated_deadline';
    const UPDATED_ASSIGN = 'updated_assign';

    public function __construct(Leads $lead)
    {
        $this->model = $lead;
    }

    public function find($id)
    {
        $model = $this->cloneModel();
        return $model->findOrFail($id);
    }

    public function create($requestData)
    {
        $fk_client_id = $requestData->get('fk_client_id');
        $input = $requestData = array_merge(
            $requestData->all(),
            ['fk_user_id_created' => \Auth::id(),
             'contact_date' => $requestData->contact_date ." " . $requestData->contact_time . ":00"]
        );

        $lead = $this->model->create($input);
        Session()->flash('flash_message', 'Lead successfully added!');

        event(new \App\Events\LeadAction($lead, self::CREATED));

        return $lead;
    }

    public function assignTenant($model, $tenant_id)
    {
       $model->tenant_id = $tenant_id;
       $model->save();
    }

    public function updateStatus($id, $requestData)
    {
        $model = $this->cloneModel();
        $lead = $model->findOrFail($id);

        $input = $requestData->get('status');
        $input = array_replace($requestData->all(), ['status' => 2]);
        $lead->fill($input)->save();
        event(new \App\Events\LeadAction($lead, self::UPDATED_STATUS));
    }

    public function updateFollowup($id, $requestData)
    {
        $model = $this->cloneModel();
        $lead = $model->findOrFail($id);
        $input = $requestData->all();
        $input = $requestData =
         [ 'contact_date' => $requestData->contact_date ." " . $requestData->contact_time . ":00"];
        $lead->fill($input)->save();
        event(new \App\Events\LeadAction($lead, self::UPDATED_DEADLINE));
    }

    public function updateAssign($id, $requestData)
    {
        $model = $this->cloneModel();
        $lead = $model->findOrFail($id);

        $input = $requestData->get('fk_user_id_assign');
        $input = array_replace($requestData->all());
        $lead->fill($input)->save();
        $insertedName = $lead->assignee->name;

        event(new \App\Events\LeadAction($lead, self::UPDATED_ASSIGN));
    }

    public function allLeads()
    {
        $model = $this->cloneModel();
        return $model->count();
    }

    public function allCompletedLeads()
    {
        $model = $this->cloneModel();
        return $model->where('status', 2)->count();
    }

    public function percantageCompleted()
    {
        if (!$this->allLeads() || !$this->allCompletedLeads()) {
            $totalPercentageLeads = 0;
        } else {
            $totalPercentageLeads =  $this->allCompletedLeads() / $this->allLeads() * 100;
        }

        return $totalPercentageLeads;
    }

    public function completedLeadsToday()
    {
        $model = $this->cloneModel();
        return $model->whereRaw(
            'date(updated_at) = ?',
            [Carbon::now()->format('Y-m-d')]
        )->where('status', 2)->count();
    }

    public function createdLeadsToday()
    {
        $model = $this->cloneModel();
        return $model->whereRaw(
            'date(created_at) = ?',
            [Carbon::now()->format('Y-m-d')]
        )->count();
    }

    public function completedLeadsThisMonth()
    {
        $model = $this->cloneModel();
        return $model
            ->select(DB::raw('count(*) as total, updated_at'))
            ->where('status', 2)
            ->whereBetween('updated_at', [Carbon::now()->startOfMonth(), Carbon::now()])->get();
    }

    public function createdLeadsMonthly()
    {
        $model = $this->cloneModel();
        return $model
            ->select(DB::raw('count(*) as month, updated_at'))
            ->where('status', 2)
            ->groupBy(DB::raw('YEAR(updated_at), MONTH(updated_at)'))
            ->get();
    }

    public function completedLeadsMonthly()
    {
        $model = $this->cloneModel();
        return $model
            ->select(DB::raw('count(*) as month, created_at'))
            ->groupBy(DB::raw('YEAR(created_at), MONTH(created_at)'))
            ->get();
    }
}
