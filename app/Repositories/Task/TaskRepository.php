<?php
namespace App\Repositories\Task;

use App\Models\Tasks;
use Notifynder;

use Carbon;
use App\Models\Activity;
use App\Models\TaskTime;
use Illuminate\Support\Facades\DB;
use App\Models\Integration;
use App\Repositories\BaseRepository;

class TaskRepository extends BaseRepository implements TaskRepositoryContract
{
    const CREATED = 'created';
    const UPDATED_STATUS = 'updated_status';
    const UPDATED_TIME = 'updated_time';
    const UPDATED_ASSIGN = 'updated_assign';

    public function __construct(Tasks $task, TaskTime $taskTime)
    {
        $this->model = $task;
        $this->taskTime = $taskTime;
    }

    public function find($id)
    {
        $model = $this->cloneModel();
        return $model->findOrFail($id);
    }

    public function getAssignedClient($id)
    {
        $model = $this->cloneModel();
        $tasks = $model->findOrFail($id);
        $tasks->clientAssignee;
        return $tasks;
    }

    public function assignTenant($model, $tenant_id)
    {
        $model->tenant_id = $tenant_id;
        $model->save();
    }

    public function GetTimeForTask($id)
    {
        $model = $this->cloneModel();
        $taskstime = $model->findOrFail($id);
        $taskstime->allTime;
        return $taskstime;
    }

    public function getTaskTime($id)
    {
        return $this->taskTime->where('fk_task_id', $id)->get();
    }


    public function create($requestData)
    {

        $fk_client_id = $requestData->get('fk_client_id');
        $input = $requestData = array_merge(
            $requestData->all(),
            ['fk_user_id_created' => auth()->id(), ]
        );

        $task = $this->model->create($input);

        Session()->flash('flash_message', 'Task successfully added!');
        event(new \App\Events\TaskAction($task, self::CREATED));

        return $task;
    }

    public function updateStatus($id, $requestData)
    {
        $model = $this->cloneModel();
        $task = $model->findOrFail($id);
        $input = $requestData->get('status');
        $input = array_replace($requestData->all(), ['status' => 2]);
        $task->fill($input)->save();
        event(new \App\Events\TaskAction($task, self::UPDATED_STATUS));
    }

    public function updateTime($id, $requestData)
    {
        $model = $this->cloneModel();
        $task = $model->findOrFail($id);
        $input = array_replace($requestData->all(), ['fk_task_id' => "$task->id"]);

        $this->taskTime->create($input);

        event(new \App\Events\TaskAction($task, self::UPDATED_TIME));
    }

    public function updateAssign($id, $requestData)
    {
        $model = $this->cloneModel();
        $task = $model->with('assignee')->findOrFail($id);

        $input = $requestData->get('fk_user_id_assign');

        $input = array_replace($requestData->all());
        $task->fill($input)->save();
        $task = $task->fresh();

        event(new \App\Events\TaskAction($task, self::UPDATED_ASSIGN));
    }

    public function invoice($id, $requestData)
    {
        $contatGuid = $requestData->invoiceContact;

        $model = $this->cloneModel();
        $taskname = $model->find($id);
        $timemanger = $this->taskTime->where('fk_task_id', $id)->get();
        $sendMail = $requestData->sendMail;
        $productlines = [];

        foreach ($timemanger as $time) {
            $productlines[] = [
              'Description' => $time->title,
              'Comments' => $time->comment,
              'BaseAmountValue' => $time->value,
              'Quantity' => $time->time,
              'AccountNumber' => 1000,
              'Unit' => 'hours'];
        }

        $api = Integration::getApi('billing');

        $results = $api->createInvoice([
            'Currency' => 'DKK',
            'Description' => $taskname->title,
            'contactId' => $contatGuid,
            'ProductLines' => $productlines]);

        if ($sendMail == true) {
            $bookGuid = $booked->Guid;
            $bookTime = $booked->TimeStamp;
            $api->sendInvoice($bookGuid, $bookTime);
        }
    }

    /**
     * Statistics for Dashboard
     */

    public function allTasks()
    {
        $model = $this->cloneModel();
        return $model->count();
    }

    public function allCompletedTasks()
    {
        $model = $this->cloneModel();
        return $model->where('status', 2)->count();
    }

    public function percantageCompleted()
    {
        if (!$this->allTasks() || !$this->allCompletedTasks()) {
            $totalPercentageTasks = 0;
        } else {
            $totalPercentageTasks =  $this->allCompletedTasks() / $this->allTasks() * 100;
        }

        return $totalPercentageTasks;
    }

    public function createdTasksMothly()
    {
        $model = $this->cloneModel();
        return $model//DB::table('tasks')
            ->select(DB::raw('count(*) as month, created_at'))
            ->groupBy(DB::raw('YEAR(created_at), MONTH(created_at)'))
            ->get();
    }

    public function completedTasksMothly()
    {
        $model = $this->cloneModel();
        return $model//DB::table('tasks')
            ->select(DB::raw('count(*) as month, updated_at'))
            ->where('status', 2)
            ->groupBy(DB::raw('YEAR(updated_at), MONTH(updated_at)'))
            ->get();
    }

    public function createdTasksToday()
    {
        $model = $this->cloneModel();
        return $model->whereRaw(
            'date(created_at) = ?',
            [Carbon::now()->format('Y-m-d')]
        )->count();
    }

    public function completedTasksToday()
    {
        $model = $this->cloneModel();
        return $model->whereRaw(
            'date(updated_at) = ?',
            [Carbon::now()->format('Y-m-d')]
        )->where('status', 2)->count();
    }

    public function completedTasksThisMonth()
    {
        $model = $this->cloneModel();
        return $model//DB::table('tasks')
            ->select(DB::raw('count(*) as total, updated_at'))
            ->where('status', 2)
            ->whereBetween('updated_at', [Carbon::now()->startOfMonth(), Carbon::now()])->get();
    }

    public function totalTimeSpent()
    {
        return $this->taskTime//DB::table('tasks_time')
            ->select(DB::raw('SUM(time)'))
            ->get();
    }
}
