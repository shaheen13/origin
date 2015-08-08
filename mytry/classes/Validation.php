<?php

class Validation {

	private $valid = true;

	public $errors_html;

	private $error_logs = array();

	public function validate($data,$directives)
	{

		foreach($directives as $fieldname=>$specifics)
		{
			$callbacks = explode('|',$specifics['rules']);
			foreach($callbacks as $callback)
			{
				$callback = explode('-',$callback); // separate restriction from the rule e.g(max_length-5)

				$value = $data[$fieldname];

				$specifics['placeholder'] = ($specifics['placeholder'])? $specifics['placeholder'] : $fieldname;   // replace placeholder by fieldname if it's missing
								// $this->func_name($value,$placeholder,$restriction,$custom_msd,$data)   $data--> needed in methods like match which uses apiece of data rather than the one to be validated
				$this->valid = ($this->$callback[0](trim($value),$specifics['placeholder'],$callback[1],$specifics['custom_msg'],$data))? $this->valid : false;
			}
		}

		$this->error_html_prepare();

		return $this->valid;

	}

	private function email($value,$placeholder,$restriction = null,$custom_msg = false,$data)
	{
		$cond = true;

		if(!empty($value) && !filter_var($value,FILTER_VALIDATE_EMAIL))
		{
			$this->error_logs[] =($custom_msg)? $custom_msg : "$placeholder isn't valid";
			$cond = false;
		}

		return $cond;
	}

	private function match($value,$placeholder,$restriction = null,$custom_msg = false,$data)
	{
		$cond = true;

		if(!empty($value) && $value !== $data["$restriction"])
		{
			$this->error_logs[] =($custom_msg)? $custom_msg : "$placeholder don't match in two fields";
			$cond = false;
		}

		return $cond;
	}

	private function required($value,$placeholder,$restriction = null,$custom_msg,$data)
	{
		$cond = true;
		if(empty($value))
		{
			$this->error_logs[] = "$placeholder is required";
			$cond = false;
		}

		return $cond;
	}

	private function min_length($value,$placeholder,$restriction = null,$custom_msg = false,$data)
	{
		$cond = true;

		if(!empty($value) && strlen($value) < $restriction)
		{
			$this->error_logs[] =($custom_msg)? $custom_msg : "$placeholder should be more than $restriction characters";
			$cond = false;
		}

		return $cond;
	}

	private function max_length($value,$placeholder,$restriction = null,$custom_msg = false,$data)
	{
		$cond = true;

		if(!empty($value) && strlen($value) > $restriction)
		{
			$this->error_logs[] =($custom_msg)? $custom_msg : "$placeholder should be less than $restriction characters";
			$cond = false;
		}

		return $cond;
	}

	private function length($value,$placeholder,$restriction = null,$custom_msg = false,$data)
	{
		$cond = true;

		if(!empty($value) && strlen($value) != $restriction)
		{
			$this->error_logs[] =($custom_msg)? $custom_msg : "$placeholder should be $restriction characters";
			$cond = false;
		}

		return $cond;
	}

	private function int_more_than($value,$placeholder,$restriction = null,$custom_msg = false,$data)
	{
		$cond = true;

		if(!empty($value) && (!is_numeric($value) || $value < $restriction))
		{
			$this->error_logs[] =($custom_msg)? $custom_msg : "$placeholder should be anumber more than $restriction ";
			$cond = false;
		}
		return $cond;
	}

	private function int_less_than($value,$placeholder,$restriction = null,$custom_msg = false,$data)
	{
		$cond = true;

		if(!empty($value) && (!is_numeric($value) || $value > $restriction))
		{
			$this->error_logs[] =($custom_msg)? $custom_msg : "$placeholder should be anumber less than $restriction ";
			$cond = false;
		}
		return $cond;
	}

	private function unique($value,$placeholder,$restriction = null,$custom_msg = false,$data)
	{
		$cond = true;

		if(!empty($value)){

			$field_data = explode('.',$restriction);
			$table = $field_data[0];
			$field = $field_data[1];

			// connection to the database with the DB class
			
			if(DB::getInstance()->count($table, array("$field", '=', "$value")) > 0)
			{

				$this->error_logs[] = ($custom_msg)? $custom_msg : "This $placeholder has been picked try another one";
				$cond = false;

			}
		}

		return $cond;
	}

	public function validate_files($files,$directives)
	{

		foreach ($directives as $fieldname=>$specifics) {
			$callbacks = explode('|',$specifics['rules']);
			foreach($callbacks as $callback)
			{
				$callback = explode('-',$callback);

				$specifics['placeholder'] = ($specifics['placeholder'])? $specifics['placeholder'] : $fieldname;   // replace placeholder by fieldname if it's missing

				$this->valid = ($this->$callback['0']($files[$fieldname],$specifics['placeholder'],$callback[1],$specifics['custom_msg'],$files))? $this->valid : false;
			}
		}

		$this->error_html_prepare();

		return $this->valid;
	}

	private function required_file($value,$placeholder,$restriction = null,$custom_msg,$data)
	{
		$cond = true;

		if($value['error'] === 4)
		{
			$this->error_logs[] =($custom_msg)? $custom_msg : "$placeholder is a required file";
			$cond = false;
		}

		return $cond;
	}

	private function max_size($value,$placeholder,$restriction = null,$custom_msg,$data)
	{
		$cond = true;

		if($value['error'] !== 4 && $value['size'] > ($restriction*1024))
		{
			$this->error_logs[] =($custom_msg)? $custom_msg : "$placeholder shoud be less than $restriction KP";
			$cond = false;
		}

		return $cond;
	}

	private function type($value,$placeholder,$restriction = null,$custom_msg,$data)
	{
		$cond = true;

		if($value['error'] !== 4)
		{
			$valid_ext = explode(',',$restriction);

			$client_ext = "." . pathinfo($value['name'],PATHINFO_EXTENSION);


			if(version_compare(PHP_VERSION, '5.3.0') >= 0)
			{
				$f_info = finfo_open(FILEINFO_MIME_TYPE);
				$real_ext = finfo_file($f_info, $value['tmp_name']);
				finfo_close($f_info);
			}
			else
			{
				$real_ext = $value['type'];
			}
			//
			// needs some handling to activate checking the real .ext for the file (D.H.T.N.M.B.L)

			$real_ext = $client_ext;

			if(!in_array($real_ext,$valid_ext) || $real_ext != $client_ext)
			{
				$this->error_logs[] =($custom_msg)? $custom_msg : "file type is invalid";
				$cond = false;
			}
		}

		return $cond;
	}

	public function addErrors($errors_array)
	{
		$this->error_logs = array_merge($this->error_logs, $errors_array);
		$this->error_html_prepare();
	}

	private function error_html_prepare()
	{
		ob_start();
		echo"<ul>";
		foreach($this->error_logs as $error)
		{
			echo "<li>" . $error . "</li>";
		}
		echo"</ul>";

		$this->errors_html = ob_get_clean();
	}
}