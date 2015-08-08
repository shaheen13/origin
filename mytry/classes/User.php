<?php

class User {
	private $_db,
			$user_data,
			$login_errors = array(),
			$session_name,
			$_cookieName,
			$_isLoggedIn = false;

	public function __construct($user = null)
	{
		$this->_db = DB::getInstance();

		$this->session_name = Config::get('session/session_name');
		$this->_cookieName = Config::get('remember/cookie_name');

		if(!$user) {
			if(Session::exists($this->session_name)) {
				if($this->find_by_id(Session::get($this->session_name))) {
					$this->_isLoggedIn = true;
				} else {
					// logout process
					$this->logout();
				}
			} elseif(Cookie::exists($this->_cookieName)) {
				$this->_db->get('user_id', 'users_session', array('hash', '=', Cookie::get($this->_cookieName)));
				if($this->find_by_id($this->_db->first()->user_id)) {
					Session::put($this->session_name, $this->data()->id);
					Cookie::put($this->_cookieName, Cookie::get($this->_cookieName), Config::get('remember/cookie_expiry'));
					Session::flash('success', 'Wellcome Back ' . $this->data()->username);
					$this->_isLoggedIn = true;
				} else {
					$this->logout();
				}
			}
		} elseif(is_numeric($user)) {
			if($this->find_by_id($user)) {
				Session::put($this->session_name, $this->data()->id);
				$this->_isLoggedIn = true;
			} else {
				$this->logout();
			}
		} elseif(is_string($user)) {
			return $this->find($user);
		}

	}

	public function create($fields = array())
	{
		if(!$this->_db->insert('users', $fields)) {
			throw new Exception("sorry, a problem with creating an account.");
		}
	}

	public function update($fields = array(), $id)
	{
		if(!$this->_db->update('users', $fields, "id = $id")) {
			throw new Exception("A problem with updating your profile");
		} else {
			$this->__construct();
		}
	}

	private function find($username)
	{
		if($this->_db->count('users', array('username', '=', $username)) == 1) {
			$this->user_data = $this->_db->first();
			return true;
		}
		return false;
	}

	private function find_by_id($user_id)
	{
		if($this->_db->count('users', array('id', '=', $user_id)) == 1) {
			$this->user_data = $this->_db->first();
			return true;
		}
		return false;
	}

	public function login($user = null, $password = null, $remember = null)
	{
		if($this->find($user) || $this->find_by_id($user)) {
			if($this->user_data->password === Hash::make($password, $this->data()->salt)) {
				Session::put($this->session_name, $this->data()->id);

				if($remember) {
					$hash = Hash::unique();
					$fields = array(
						"user_id" => $this->data()->id,
						"hash"	  => $hash
						);
					if($this->_db->insert('users_session', $fields)) {
						COOKIE::put($this->_cookieName, $hash, Config::get('remember/cookie_expiry'));
					}
				}

				return true;
			} else {
				$this->user_data = null;
				$this->login_errors['password'] = "password is incorrect";
				return false;
			}
		} else {
			$this->login_errors['user'] = "user not found in databases";
			return false;
		}
		return false;
	}

	public function logout()
	{
		Cookie::delete($this->_cookieName, Config::get('remember/cookie_expiry'));
		$this->_db->delete('users_session', array('user_id', '=', Session::get($this->session_name)));
		Session::delete($this->session_name);
	}

	public function data()
	{
		return $this->user_data;
	}

	public function hasPermission($key)
	{
		$groub = $this->_db->get('*' , 'groups', array('group_id', '=', $this->data()->user_group));
		if($this->_db->count()) {
			$permissions = $this->_db->first()->permissions;
			$permissions = json_decode($permissions, true);
			if($permissions["$key"] == true){
				$this->user_data->group = $this->_db->first()->name;
				return true;
			}
		}
		return FALSE;
	}

	public function isLoggedIn()
	{
		return $this->_isLoggedIn;
	}

	public function loginErrors()
	{
		return $this->login_errors;
	}
}