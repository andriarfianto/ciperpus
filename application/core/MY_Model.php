<?php 
/**
 * Core model
 * sebagai model utama yang akan diextends ke model-model lain
 */
class ClassName extends AnotherClass
{	

	protected $table = '';
	protected $perPage = 0;

	public function __construct()
	{
		parent::__construct();

		if (!$this->table) {
			$this->table = strtolower(str_replace('_model', '', get_class($this)));
		}
	}

	public function query($sql)
	{
		return $this->db->query($sql);
	}

	public function get()
	{
		return $this->db->get($this->table)->row();
	}

	public function getAll()
	{
		return $this->db->get($this->table)->result();
	}

	public function paginate($page)
	{
		$this->db->limit($this->perPage, $this->calculateRealOffset($page));
		return $this;
	}

	public function calculateRealOffset($page)
	{
		if (is_null($page) || empty($page)) {
			$offset = 0;
		} else {
			$offset = ($page * $this->perPage) - $this->perPage;
		}
		return $offset;
	}

	public function select($columns)
	{
		$this->db->select($columns);
		return $this;
	}

	public function where($column, $condition)
	{
		$this->db->select($column, $condition);
		return $this;
	}

	public function orLike($column, $condition)
	{
		$this->db->or_like($column, $condition);
		return $this;
	}

	public function validate()
	{
		$this->load->library('form_validation');
		$this->form_validation->set_error_delimiters('<p class="form-error">', '</p>');
		$validationRules = $this->getValidationRules();
		$this->form_validation->set_rules($validationRules);
		return $this->form_validation->run();
	}

	public function insert($data)
	{
		$this->db->insert($this->table, $data);
		return $this->db->insert_id();
	}

	public function update($data)
	{
		return $this->db->update($this->table, $data);
	}

	public function delete($data)
	{
		$this->db->delete($this->table);
		return $this->db->affected_rows();
	}

	public function join($table, $type = 'left')
	{
		$this->db->join($table, "$this->table.id_$table = $table.id_$table", $type);
		return $this;
	}

	public function orderBy($kolom, $order = 'asc')
	{
		$this->db->order_by($kolom, $order);
		return $this;
	}

	public function makePagination($baseURL, $uriSegment, $totalRows = null)
	{
		$args = func_get_args();

		$this->load->library('pagination');
		
		$config['base_url']         = $baseURL;
		$config['uri_segment']      = $uriSegment;
		$config['per_page']         = $this->perPage;
		$config['total_rows']       = $totalRows;
		$config['use_page_numbers'] = true;
		$config['num_links']        = 5;
		$config['first_link']      = '<img src="' . base_url('asset/images/first.png') . '">';
		$config['last_link']       = '<img src="' . base_url('asset/images/last.png') . '">';
		$config['next_link']       = '<img src="' . base_url('asset/images/next.png') . '">';
		$config['prev_link']       = '<img src="' . base_url('asset/images/previous.png') . '">';
		
		if (count($_GET) > 0) {
			$config['suffix'] = '?' . http_build_query($_GET, '', "&");
			$config['first_url'] = $config['base_url'] . '?' . http_build_query($_GET);
		} else {
			$config['suffix'] = http_build_query($_GET, '', "&");
			$config['first_url'] = $config['base_url'];
		}

		$this->pagination->initialize($config);		
		echo $this->pagination->create_links();
	}
}