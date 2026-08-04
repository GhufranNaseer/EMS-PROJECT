<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Ignited Datatables
 *
 * This is a wrapper class/library based on the native Datatables server-side implementation by Allan Jardine
 * found at http://datatables.net/examples/data_sources/server_side.html for CodeIgniter
 *
 * @package    CodeIgniter
 * @subpackage libraries
 * @category   library
 * @version    0.7
 * @author     Vincent Bambico <metal.conspiracy@gmail.com>
 *             Yusuf Ozdemir <yusuf@ozdemir.be>
 * @link       http://codeigniter.com/forums/viewthread/160896/
 */

/*
 * miniMAX Extended Version
 * extended by: Raheel Khan
 * version: 1.0
 */
class Datatables
{
	/**
	 * Global container variables for chained argument results
	 *
	 */
	protected $ci;
	protected $table;
	protected $distinct;
	protected $group_by;
	protected $select         = array();
	protected $joins          = array();
	protected $columns        = array();
	protected $where          = array();
	protected $like          = array();
	protected $filter         = array();
	protected $add_columns    = array();
	protected $edit_columns   = array();
	protected $unset_columns  = array();

	/**
	 * Copies an instance of CI
	 */
	public function __construct()
	{
		$this->ci =& get_instance();
	}

	/**
	 * If you establish multiple databases in config/database.php this will allow you to
	 * set the database (other than $active_group) - more info: http://codeigniter.com/forums/viewthread/145901/#712942
	 */
	public function set_database($db_name)
	{
		$db_data = $this->ci->load->database($db_name, TRUE);
		$this->ci->db = $db_data;
	}

	/**
	 * Generates the SELECT portion of the query
	 *
	 * @param string $columns
	 * @param bool $backtick_protect
	 * @return mixed
	 */
	public function select($columns, $backtick_protect = TRUE)
	{
		foreach($this->explode(',', $columns) as $val)
		{
			$val = trim($val);
			if ($val === '') {
				continue;
			}
			$column = trim(preg_replace('/(.*)\s+as\s+(\w*)/i', '$2', $val));
			$this->columns[] =  $column;
			$this->select[$column] =  trim(preg_replace('/(.*)\s+as\s+(\w*)/i', '$1', $val));
		}

		$columns = trim($columns);
		$columns = rtrim($columns, ',');

		$this->ci->db->select($columns, $backtick_protect);
		return $this;
	}

	/**
	 * Generates the DISTINCT portion of the query
	 *
	 * @param string $column
	 * @return mixed
	 */
	public function distinct($column)
	{
		$this->distinct = $column;
		$this->ci->db->distinct($column);
		return $this;
	}

	/**
	 * Generates the GROUP_BY portion of the query
	 *
	 * @param string $column
	 * @return mixed
	 */
	public function group_by($column)
	{
		$this->group_by = $column;
		$this->ci->db->group_by($column);
		return $this;
	}

	/**
	 * Generates the FROM portion of the query
	 *
	 * @param string $table
	 * @return mixed
	 */
	public function from($table)
	{
		$this->table = $table;
		$this->ci->db->from($table);
		return $this;
	}

	/**
	 * Generates the JOIN portion of the query
	 *
	 * @param string $table
	 * @param string $fk
	 * @param string $type
	 * @return mixed
	 */
	public function join($table, $fk, $type = NULL)
	{
		$this->joins[] = array($table, $fk, $type);
		$this->ci->db->join($table, $fk, $type);
		return $this;
	}

	/**
	 * Generates the WHERE portion of the query
	 *
	 * @param mixed $key_condition
	 * @param string $val
	 * @param bool $backtick_protect
	 * @return mixed
	 */
	public function where($key_condition, $val = NULL, $backtick_protect = TRUE)
	{
		$this->where[] = array($key_condition, $val, $backtick_protect);
		$this->ci->db->where($key_condition, $val, $backtick_protect);
		return $this;
	}

	// hack for where in
	public function where_in($key_condition, $val = [], $backtick_protect = TRUE)
	{
		$this->where[] = array($key_condition, $val[0], $backtick_protect); // $val[0] is a hack because array is not support in datatable
		$this->ci->db->where_in($key_condition, $val, $backtick_protect);
		return $this;
	}

	/**
	 * Generates the WHERE portion of the query
	 *
	 * @param mixed $key_condition
	 * @param string $val
	 * @param bool $backtick_protect
	 * @return mixed
	 */
	public function or_where($key_condition, $val = NULL, $backtick_protect = TRUE)
	{
		$this->where[] = array($key_condition, $val, $backtick_protect);
		$this->ci->db->or_where($key_condition, $val, $backtick_protect);
		return $this;
	}

	/**
	 * Generates the WHERE portion of the query
	 *
	 * @param mixed $key_condition
	 * @param string $val
	 * @param bool $backtick_protect
	 * @return mixed
	 */
	public function like($key_condition, $val = NULL, $backtick_protect = TRUE)
	{
		$this->like[] = array($key_condition, $val, $backtick_protect);
		$this->ci->db->like($key_condition, $val, $backtick_protect);
		return $this;
	}

	/**
	 * Generates the WHERE portion of the query
	 *
	 * @param mixed $key_condition
	 * @param string $val
	 * @param bool $backtick_protect
	 * @return mixed
	 */
	public function filter($key_condition, $val = NULL, $backtick_protect = TRUE)
	{
		$this->filter[] = array($key_condition, $val, $backtick_protect);
		return $this;
	}

	/**
	 * Sets additional column variables for adding custom columns
	 *
	 * @param string $column
	 * @param string $content
	 * @param string $match_replacement
	 * @return mixed
	 */
	public function add_column($column, $content, $match_replacement = NULL , $colNo = NULL)
	{
		$this->add_columns[$column] = array('content' => $content, 'replacement' => $this->explode(',', $match_replacement) , 'colNo' => $colNo);
		return $this;
	}

	/**
	 * Sets additional column variables for editing columns
	 *
	 * @param string $column
	 * @param string $content
	 * @param string $match_replacement
	 * @return mixed
	 */
	public function edit_column($column, $content, $match_replacement)
	{
		$this->edit_columns[$column][] = array('content' => $content, 'replacement' => $this->explode(',', $match_replacement));
		return $this;
	}

	/**
	 * Unset column
	 *
	 * @param string $column
	 * @return mixed
	 */
	public function unset_column($column)
	{
		$this->unset_columns[] = $column;
		return $this;
	}

	/**
	 * Builds all the necessary query segments and performs the main query based on results set from chained statements
	 *
	 * @param string charset
	 * @return string
	 */
	public function generate($charset = 'UTF-8', $export_csv = true)
	{
		// enable export
		if (!$this->ci->input->is_ajax_request() && $export_csv) {
			$csv_headers = $this->ci->input->get('headers');
			$file_name = null;

			if (isset($csv_headers)) {
				$csv_headers = $this->explode(',', $csv_headers);
				$csv_headers = array_filter($csv_headers, function($value) { return $value !== ''; });
			} else {
				$csv_headers = null;
			}

			if ($this->ci->input->get('file_name')) {
				$file_name = $this->ci->input->get('file_name');
			}

			if ($this->ci->input->get('export_type')) {
				if ($this->ci->input->get('export_type') == 'csv') {
					$this->generate_csv($charset, $file_name, $csv_headers);
				} else if ($this->ci->input->get('export_type') == 'excel') {
					$this->generate_excel($charset, $file_name, $csv_headers);
				} else if ($this->ci->input->get('export_type') == 'pdf') {
					$this->generate_pdf($charset, $file_name, $csv_headers);
				}
			} else {
				$this->generate_csv($charset, $file_name, $csv_headers);
			}
			return;
		}
		$this->get_paging();
		$this->get_ordering();
		$this->get_filtering();
		return $this->produce_output($charset);
	}

	/**
	 * Generates the LIMIT portion of the query
	 *
	 * @return mixed
	 */
	protected function get_paging()
	{
		$iStart = $this->ci->input->post('iDisplayStart');
		$iLength = $this->ci->input->post('iDisplayLength');
		$this->ci->db->limit(($iLength != '' && $iLength != '-1')? $iLength : 100, ($iStart)? $iStart : 0);
	}

	/**
	 * Generates the ORDER BY portion of the query
	 *
	 * @return mixed
	 */
	protected function get_ordering()
	{
		if($this->check_mDataprop())
			$mColArray = $this->get_mDataprop();
		elseif($this->ci->input->post('sColumns'))
			$mColArray = explode(',', $this->ci->input->post('sColumns'));
		else
			$mColArray = $this->columns;

		$mColArray = array_values(array_diff($mColArray, $this->unset_columns));
		$columns = array_values(array_diff($this->columns, $this->unset_columns));

		for($i = 0; $i < intval($this->ci->input->post('iSortingCols')); $i++)
			if(isset($mColArray[intval($this->ci->input->post('iSortCol_' . $i))]) && in_array($mColArray[intval($this->ci->input->post('iSortCol_' . $i))], $columns) && $this->ci->input->post('bSortable_'.intval($this->ci->input->post('iSortCol_' . $i))) == 'true')
				$this->ci->db->order_by($mColArray[intval($this->ci->input->post('iSortCol_' . $i))], $this->ci->input->post('sSortDir_' . $i));
	}

	/**
	 * Generates the LIKE portion of the query
	 *
	 * @return mixed
	 */
	protected function get_filtering()
	{
		if($this->check_mDataprop())
			$mColArray = $this->get_mDataprop();
		elseif($this->ci->input->post('sColumns'))
			$mColArray = explode(',', $this->ci->input->post('sColumns'));
		else
			$mColArray = $this->columns;

		$sWhere = '';
		$sSearch = $this->ci->db->escape_str($this->ci->input->post('sSearch'));
		$mColArray = array_values(array_diff($mColArray, $this->unset_columns));
		$columns = array_values(array_diff($this->columns, $this->unset_columns));

		if($sSearch != '')
			for($i = 0; $i < count($mColArray); $i++)
				if($this->ci->input->post('bSearchable_' . $i) == 'true' && in_array($mColArray[$i], $columns))
					$sWhere .= $this->select[$mColArray[$i]] . " LIKE '%" . $sSearch . "%' OR ";

		$sWhere = substr_replace($sWhere, '', -3);

		if($sWhere != '')
			$this->ci->db->where('(' . $sWhere . ')');

		for($i = 0; $i < intval($this->ci->input->post('iColumns')); $i++)
		{
			if(isset($_POST['sSearch_' . $i]) && $this->ci->input->post('sSearch_' . $i) != '' && in_array($mColArray[$i], $columns))
			{
				$miSearch = explode(',', $this->ci->input->post('sSearch_' . $i));

				foreach($miSearch as $val)
				{
					if(preg_match("/(<=|>=|=|<|>)(\s*)(.+)/i", trim($val), $matches))
						$this->ci->db->where($this->select[$mColArray[$i]].' '.$matches[1], $matches[3]);
					else
						$this->ci->db->where($this->select[$mColArray[$i]].' LIKE', '%'.$val.'%');
				}
			}
		}

		foreach($this->filter as $val)
			$this->ci->db->where($val[0], $val[1], $val[2]);
	}

	/**
	 * Compiles the select statement based on the other functions called and runs the query
	 *
	 * @return mixed
	 */
	protected function get_display_result()
	{
		$data = $this->ci->db->get();
		return $data;
	}

	/**
	 * Builds a JSON encoded string data
	 *
	 * @param string charset
	 * @return string
	 */
	protected function produce_output($charset)
	{
		$aaData = array();
		$rResult = $this->get_display_result();
		$iTotal = $this->get_total_results();
		$iFilteredTotal = $this->get_total_results(TRUE);

		foreach($rResult->result_array() as $row_key => $row_val)
		{
			$aaData[$row_key] = ($this->check_mDataprop())? $row_val : array_values($row_val);

			foreach($this->add_columns as $field => $val)
			{
				if (!is_string($val['content']) && is_callable($val['content']))
				{
					$val['content'] = $val['content']($row_val);
					$aaData[$row_key][$field] =  $val['content'];
				}
				else
				{
					if($this->check_mDataprop())
						$aaData[$row_key][$field] = $this->exec_replace($val, $aaData[$row_key]);
					else
						$aaData[$row_key][] = $this->exec_replace($val, $aaData[$row_key]);
				}
			}




			foreach($this->edit_columns as $modkey => $modval)
				foreach($modval as $val)
					$aaData[$row_key][($this->check_mDataprop())? $modkey : array_search($modkey, $this->columns)] = $this->exec_replace($val, $aaData[$row_key]);




			$aaData[$row_key] = array_diff_key($aaData[$row_key], ($this->check_mDataprop())? $this->unset_columns : array_intersect($this->columns, $this->unset_columns));



			if(!$this->check_mDataprop())
			{
				$aaData[$row_key] = array_values($aaData[$row_key]);
				$tblCols = sizeof ( $row_val) - sizeof($this->unset_columns);

				$index = 0;
				foreach($this->add_columns as $field => $val)
				{
					if(is_numeric($val['colNo']))
					{
						$colNo = $val['colNo']-1;

						if (isset($aaData[$row_key][$colNo]))
						{
							$filedIndex = $index + $tblCols;

							$original = $aaData[$row_key];
							$inserted = array( $aaData[$row_key][$filedIndex] );
							array_splice( $original, $colNo, 0, $inserted );


							unset ($original[$filedIndex+1]);

							$original = array_values($original);
							$aaData[$row_key] =  $original;
						}
					}
					$index++;
				}
			}

		}




		$sColumns = array_diff($this->columns, $this->unset_columns);
		$sColumns = array_merge_recursive($sColumns, array_keys($this->add_columns));

		$sOutput = array
		(
			'sEcho'                => intval($this->ci->input->post('sEcho')),
			'iTotalRecords'        => $iTotal,
			'iTotalDisplayRecords' => $iFilteredTotal,
			'aaData'               => $aaData,
			'sColumns'             => implode(',', $sColumns)
		);

		if(strtolower($charset) == 'utf-8')
			return json_encode($sOutput);
		else
			return $this->jsonify($sOutput);
	}


	public function generate_csv ($charset = 'UTF-8', $fileName = null , $header = null , $callback = null) {

		if (!is_string($fileName))
			$fileName = "report-". date('Y-m-d') . '.csv';
		else
			$fileName = $fileName . '.csv';

		$file = tmpfile();
		if ( is_array($header) )
			fputcsv($file , $header);

		//$this->get_paging();
		$this->get_ordering();
		$this->get_filtering();
		$records = $this->produce_output($charset);
		$record = json_decode($records)->aaData;

		$col_map_str = $this->ci->input->get('col_map');
		$col_map = ($col_map_str !== null && $col_map_str !== '') ? explode(',', $col_map_str) : null;

		foreach ($record as $key => $row) {
			if (is_callable($callback))
				$row = $callback($row);

			if (is_array($col_map) && !empty($col_map)) {
				$ordered_row = array();
				foreach ($col_map as $c_idx) {
					if (is_object($row) && isset($row->$c_idx)) {
						$ordered_row[] = $row->$c_idx;
					} else if (is_array($row) && isset($row[$c_idx])) {
						$ordered_row[] = $row[$c_idx];
					} else {
						$ordered_row[] = '';
					}
				}
				$row = $ordered_row;
			}

			if ($header && $header[0] == 'S.no') {
				$row[0] = ($key + 1);
			}

			foreach ($row as $k => $r) {
				$row[$k] = strip_tags($r);
			}

			fputcsv($file , $row);
		}


		header('Content-Type: text/csv');
		header('Content-Disposition: attachment; filename='. $fileName .'');
		header('Pragma: no-cache');


		rewind($file);
		fpassthru($file);
	}

	public function generate_excel ($charset = 'UTF-8', $fileName = null , $header = null , $callback = null) {

		if (!is_string($fileName))
			$fileName = "report-". date('Y-m-d') . '.xls';
		else
			$fileName = $fileName . '.xls';


		//$this->get_paging();
		$this->get_ordering();
		$this->get_filtering();
		$records = $this->produce_output($charset);
		$record = json_decode($records)->aaData;


		$html = '<html>';
		$html .= '<meta http-equiv="Content-Type" content="text/html; charset=Windows-1252">';
		$html .= '<head>';
		$html .= '<style>
		*, body {
		background: transparent;
		}
		table, td {
			border: 1px solid #ccc;
		}
		</style>';
		$html .= '</head>';
		$html .= '<body>';

		$html .= '<table class="table table-bordered"><thead>';

		if ($this->ci->input->get('event_id') && $this->ci->input->get('event_id') != '') {
			$event_dates = $this->ci->db
				->where(mycolumn('exhibition_id'), $this->ci->input->get('event_id'))
				->get('es_exhibition_date')
				->result();

			if (!empty($event_dates)) {
				$html .= '<tr><th colspan="'.count($header).'" align="left"><h4>Report: '.(explode('.', $fileName)[0]).'</h4></th></tr>';
				$html .= '<tr><td colspan="'.count($header).'" align="right">Print Date: '.date('d/m/Y h:i A').'</td></tr>';
				$html .= '<tr><td colspan="'.count($header).'" align="right">Event Date: '.date('d/m/Y', strtotime($event_dates[0]->date)).' - '.date('d/m/Y', strtotime(end($event_dates)->date)).'</td></tr>';
				$html .= '<tr><td colspan="'.count($header).'"></td></tr>';
			}
		}

		$html .= '<tr>';
		foreach ($header as $h) {
			$html .= '<th>'.$h.'</th>';
		}
		$html .= '</tr></thead><tbody>';

		$col_map_str = $this->ci->input->get('col_map');
		$col_map = ($col_map_str !== null && $col_map_str !== '') ? explode(',', $col_map_str) : null;

		foreach ($record as $key => $row) {
			if (is_callable($callback))
				$row = $callback($row);

			if (is_array($col_map) && !empty($col_map)) {
				$ordered_row = array();
				foreach ($col_map as $c_idx) {
					if (is_object($row) && isset($row->$c_idx)) {
						$ordered_row[] = $row->$c_idx;
					} else if (is_array($row) && isset($row[$c_idx])) {
						$ordered_row[] = $row[$c_idx];
					} else {
						$ordered_row[] = '';
					}
				}
				$row = $ordered_row;
			}

			$html .= '<tr>';
			foreach ($row as $k => $r) {
				if ($k == 0) {
					$html .= '<td>'.($key + 1).'</td>';
				} else {
					$html .= '<td>'.strip_tags($r).'</td>';
				}
			}
			$html .= '</tr>';
		}

		$html .= "</tbody>";
		$html .= "</table>";
		$html .= "</body>";
		$html .= "</html>";

		header("Content-Type: application/vnd.ms-excel; charset=utf-8");
		header("Content-Disposition: attachment;filename=".$fileName);
		header("Cache-Control: max-age=0");
		echo $html;
		die();
	}

	public function generate_pdf ($charset = 'UTF-8', $fileName = null , $header = null , $callback = null) {

		if (!is_string($fileName))
			$fileName = "report-". date('Y-m-d');
		else
			$fileName = $fileName;


		//$this->get_paging();
		$this->get_ordering();
		$this->get_filtering();
		$records = $this->produce_output($charset);
		$record = json_decode($records)->aaData;

		$html = '<page>';
		$html .= '<style>
				* {
					-webkit-box-sizing: border-box;
					-moz-box-sizing: border-box;
					box-sizing: border-box;
					margin: 0;
					padding: 0;
				}
				page {
					font-family: Helvetica, Arial, sans-serif;
					font-size: 12px;
				}
				.text-right {
					text-align: right;
				}
				.text-left {
					text-align: left;
				}
				.text-center {
					text-align: center;
				}
				.table-no-border {
					margin: 0 !important;
				}
				table {
					border-spacing: 0;
					border-collapse: collapse;
					-webkit-box-sizing: border-box;
					-moz-box-sizing: border-box;
					box-sizing: border-box;
					width: 100%;
				}
				table th,
				table td {
					padding: 5px;
					word-break: break-all;
					white-space: normal;
					vertical-align: top;
				}
				.table-no-border,
				.table-no-border tr,
				.table-no-border th,
				.table-no-border td{
					border: none;
				}
			</style>';


		$html .= '<h4 class="text-center">'.$fileName.'</h4><hr>';
		$html .= '<table class="table table-bordered"><thead><tr>';

		foreach ($header as $h) {
			$html .= '<th width="'. (100 / count($header)) .'%">'.wordwrap($h, 20, '<br />', true).'</th>';
		}
		$html .= '</tr></thead><tbody>';

		foreach ($record as $key => $row) {
			if (is_callable($callback))
				$row = $callback($row);

			$html .= '<tr>';
			foreach ($row as $k => $r) {
				if ($k == 0) {
					$html .= '<td width="'. (100 / count($header)) .'%">'.($key + 1).'</td>';
				} else {
					$html .= '<td width="'. (100 / count($header)) .'%">'.wordwrap(strip_tags($r), 20, '<br />', true).'</td>';
				}
			}
			$html .= '</tr>';
		}
		$html .= "</tbody>";
		$html .= "</table>";
		$html .= "</page>";

		//echo $html; die();
		set_time_limit(0);
		$this->ci->load->helper ("pdf-loader");
		try {
			$html2pdf = new HTML2PDF('L', 'A4', 'en');
			$html2pdf->pdf->SetDisplayMode('fullpage');
			$html2pdf->writeHTML($html);
			$html2pdf->Output($fileName.'.pdf');
		}
		catch(HTML2PDF_exception $e) {
			$msg = $e->getMessage ();
			//$msg = base64_encode($msg);
			//$msg = trim ($msg);
			print_r($msg);
		}

	}

	/**
	 * Get result count
	 *
	 * @return integer
	 */
	protected function get_total_results($filtering = FALSE)
	{
		if($filtering)
			$this->get_filtering();


		foreach($this->joins as $val)
			$this->ci->db->join($val[0], $val[1], $val[2]);


		foreach($this->where as $val)
			$this->ci->db->where($val[0], $val[1], $val[2]);

		foreach($this->like as $val)
			$this->ci->db->like($val[0], $val[1], $val[2]);

		return $this->ci->db->count_all_results($this->table);
	}

	/**
	 * Runs callback functions and makes replacements
	 *
	 * @param mixed $custom_val
	 * @param mixed $row_data
	 * @return string $custom_val['content']
	 */
	protected function exec_replace($custom_val, $row_data)
	{
		$replace_string = '';

		if(isset($custom_val['replacement']) && is_array($custom_val['replacement']))
		{
			foreach($custom_val['replacement'] as $key => $val)
			{
				$sval = preg_replace("/(?<!\w)([\'\"])(.*)\\1(?!\w)/i", '$2', trim($val));

				if(preg_match('/(\w+)\((.*)\)/i', $val, $matches) && function_exists($matches[1]))
				{
					$func = $matches[1];
					$args = preg_split("/[\s,]*\\\"([^\\\"]+)\\\"[\s,]*|" . "[\s,]*'([^']+)'[\s,]*|" . "[,]+/", $matches[2], 0, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);

					foreach($args as $args_key => $args_val)
					{
						$args_val = preg_replace("/(?<!\w)([\'\"])(.*)\\1(?!\w)/i", '$2', trim($args_val));
						$args[$args_key] = (in_array($args_val, $this->columns))? ($row_data[($this->check_mDataprop())? $args_val : array_search($args_val, $this->columns)]) : $args_val;
					}

					$replace_string = call_user_func_array($func, $args);
				}
				elseif(in_array($sval, $this->columns))
					$replace_string = $row_data[($this->check_mDataprop())? $sval : array_search($sval, $this->columns)];
				else
					$replace_string = $sval;

				$custom_val['content'] = str_ireplace('$' . ($key + 1), $replace_string, $custom_val['content']);
			}
		}

		return $custom_val['content'];
	}

	/**
	 * Check mDataprop
	 *
	 * @return bool
	 */
	protected function check_mDataprop()
	{
		if(!$this->ci->input->post('mDataProp_0'))
			return FALSE;

		for($i = 0; $i < intval($this->ci->input->post('iColumns')); $i++)
			if(!is_numeric($this->ci->input->post('mDataProp_' . $i)))
				return TRUE;

		return FALSE;
	}

	/**
	 * Get mDataprop order
	 *
	 * @return mixed
	 */
	protected function get_mDataprop()
	{
		$mDataProp = array();

		for($i = 0; $i < intval($this->ci->input->post('iColumns')); $i++)
			$mDataProp[] = $this->ci->input->post('mDataProp_' . $i);

		return $mDataProp;
	}

	/**
	 * Return the difference of open and close characters
	 *
	 * @param string $str
	 * @param string $open
	 * @param string $close
	 * @return string $retval
	 */
	protected function balanceChars($str, $open, $close)
	{
		$openCount = substr_count($str, $open);
		$closeCount = substr_count($str, $close);
		$retval = $openCount - $closeCount;
		return $retval;
	}

	/**
	 * Explode, but ignore delimiter until closing characters are found
	 *
	 * @param string $delimiter
	 * @param string $str
	 * @param string $open
	 * @param string $close
	 * @return mixed $retval
	 */
	protected function explode($delimiter, $str, $open = '(', $close=')')
	{
		$retval = array();
		$hold = array();
		$balance = 0;
		$parts = explode($delimiter, $str);

		foreach($parts as $part)
		{
			$hold[] = $part;
			$balance += $this->balanceChars($part, $open, $close);

			if($balance < 1)
			{
				$retval[] = implode($delimiter, $hold);
				$hold = array();
				$balance = 0;
			}
		}

		if(count($hold) > 0)
			$retval[] = implode($delimiter, $hold);

		return $retval;
	}

	/**
	 * Workaround for json_encode's UTF-8 encoding if a different charset needs to be used
	 *
	 * @param mixed result
	 * @return string
	 */
	protected function jsonify($result = FALSE)
	{
		if(is_null($result))
			return 'null';

		if($result === FALSE)
			return 'false';

		if($result === TRUE)
			return 'true';

		if(is_scalar($result))
		{
			if(is_float($result))
				return floatval(str_replace(',', '.', strval($result)));

			if(is_string($result))
			{
				static $jsonReplaces = array(array('\\', '/', '\n', '\t', '\r', '\b', '\f', '"'), array('\\\\', '\\/', '\\n', '\\t', '\\r', '\\b', '\\f', '\"'));
				return '"' . str_replace($jsonReplaces[0], $jsonReplaces[1], $result) . '"';
			}
			else
				return $result;
		}

		$isList = TRUE;

		for($i = 0, reset($result); $i < count($result); $i++, next($result))
		{
			if(key($result) !== $i)
			{
				$isList = FALSE;
				break;
			}
		}

		$json = array();

		if($isList)
		{
			foreach($result as $value)
				$json[] = $this->jsonify($value);

			return '[' . join(',', $json) . ']';
		}
		else
		{
			foreach($result as $key => $value)
				$json[] = $this->jsonify($key) . ':' . $this->jsonify($value);

			return '{' . join(',', $json) . '}';
		}
	}
}
/* End of file Datatables.php */
/* Location: ./application/libraries/Datatables.php */