<?php

namespace App\Controllers;

use App\Services\AuthService;
use Exception;
use Kernel\Exceptions\NotFoundException;
use Kernel\Exceptions\ValidationException;
use Kernel\Validator;

use App\Models\Task;

class TaskController {

    private function validateTask($request) {
        $validator = new Validator($request);

        $validator->validateRequired('title');
        $validator->validateStringMin('title', 5);
        $validator->validateStringMax('title', 100);
        $validator->validateRequired('description');
        $validator->validateStringMin('description', 50);
        $validator->validateStringMax('description', 1000);

        if(!$validator->isValid()) {
            $errors = $validator->getErrors();
            throw new ValidationException($errors);
        }
    }

    private function changePoints($id, $value) {
        $taskModel = new Task();
        $task = $taskModel->where('id', '=', $id)->first();
        if($task) {
            $points = $task->points += $value;
            $taskPoints = $points < 0 ? 0 : $points;
            $task->update([
                'points' => $taskPoints
            ])->where('id', '=', $id);
            $task->save();
            return $taskPoints;
        }

        throw new NotFoundException();

    }

    public function getAll($request) {
        $taskModel = new Task();
        $tasks = array_key_exists('search', $request) 
            ? $taskModel->where('title', 'like', '%'.$request['search'].'%')->get()
            : $taskModel->get();
        return $tasks;
    }
    
    public function getById($id) {
        $taskModel = new Task();
        $test = $taskModel->where('id', '=', $id)->first();
        return $test;
    }

    public function create($request) {
        $this->validateTask($request);
        $task = new Task();
        $task->create([
            'title' => $request['title'],
            'description' => $request['description'],
            'user_id' => AuthService::user()->id
        ]);
        try {
            return ["id"=> $task->save()];
        } catch (Exception) {
            throw new Exception('Could not create the task');        
        }         
    }

    public function update($id, $request) {
        $this->validateTask($request);
        $taskModel = new Task();
        $task = $taskModel->where('id', '=', $id)->first();
        if($task) {
            $task->update([
                'title' => $request['title'],
                'description' => $request['description'],
            ])->where('id', '=', $id);
            
            try {
                $task->save();
                return ["id"=> $id];
            } catch (Exception) {
                throw new Exception('Could not update the task');        
            }   
        }
        throw new NotFoundException();
    }
    
    public function delete($id) {
        $taskModel = new Task();
        $taskModel->where('id', '=', $id)->delete();
        return;
    }

    public function increasePoints($id) {
        return $this->changePoints($id, 1);
    }

    public function decreasePoints($id) {
        return $this->changePoints($id, -1);
    }

    public function complete($id) {
        $taskModel = new Task();
        $task = $taskModel->where('id', '=', $id)->first();
        if($task) {
            if($task->status === 'completed') {
                throw new Exception('Task is already completed', 422);
            }

            $task->update([
                'status' => 'completed',
            ])->where('id', '=', $id);
            $task->save();
        } else {
            throw new NotFoundException();
        }
    }
}