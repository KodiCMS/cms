<?php defined('SYSPATH') or die('No direct script access.');

class Model_Job_log extends ORM {
	
	protected $_created_column = array(
		'column' => 'created_on',
		'format' => 'Y-m-d H:i:s'
	);
	
	protected $_updated_column = array(
		'column' => 'updated_on',
		'format' => 'Y-m-d H:i:s'
	);
	
	protected $_belongs_to = array(
		'job' => array('model' => 'job')
	);
	
	/**
	 * 
	 * @return atring
	 */
	public function status()
	{
		return Arr::get(Model_Job::statuses(), $this->status, Model_Job::STATUS_NEW);
	}

	/**
	 * 
	 * @param integer $status
	 * @return Model_Job
	 * @throws Kohana_Exception
	 */
	public function set_status($status)
	{
		if( ! $this->loaded() )
			throw new Kohana_Exception('Cannot set status because it is not loaded.');

		$this->job->set_status($status);
		$this->status = $status;
		return $this->save();
	}
	
	/**
	 * 
	 * @return \Model_Job_log
	 */
	public function complete()
	{
		$this->status = Model_Job::STATUS_COMPLETE;
		$this->save();
		
		$this->job->complete();
		
		return $this;
	}
	
	/**
	 * 
	 * @return \Model_Job_log
	 */
	public function failed()
	{
		$this->status = Model_Job::STATUS_FAILED;
		$this->save();
		
		$this->job->failed();
		
		return $this;
	}
	
	/**
	 * 
	 * @param Model_Job $job
	 * @return void
	 */
	public function run( Model_Job $job )
	{
		if(Kohana::$profiling === TRUE)
		{
			$benchmark = Profiler::start('Rub job', $job->name);
		}
			
		$this->values(array(
			'job_id' => $job->id
		))->create();
		
		$this->set_status(Model_Job::STATUS_RUNNING);
		
		try 
		{
			$job = $job->job;

			$minion_task = Minion_Task::convert_task_to_class_name($job);

			if (is_array($job))
			{
				$passed = call_user_func($job);
			}
			elseif (class_exists($minion_task))
			{
				Minion_Task::factory(array($job))->execute();
				$passed = TRUE;
			}
			elseif (strpos($job, '::') === FALSE)
			{
				$function = new ReflectionFunction($job);
				$passed = $function->invoke();
			}
			else
			{
				list($class, $method) = explode('::', $job, 2);
				$method = new ReflectionMethod($class, $method);
				$passed = $method->invoke(NULL);
			}
		} 
		catch (Exception $e) 
		{
			$this->failed();
			return;
		}
		
		$this->complete();
			
		if(isset($benchmark))
		{
			Profiler::stop($benchmark);
		}
	}
}