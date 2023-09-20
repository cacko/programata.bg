<?php

class Poll
{
	private $pollHTML;
	private $XML;
	private $results;
	private $page;
	private $ID;
	private $TABLE_USERS;
	private $TABLE_ENTRIES;
	private $TABLE_TEXTS;
	private $TABLE_TEXT_ENTRIES;

	public function __construct($file)
	{
		$this->XML = simplexml_load_file($file);
		$this->ID = trim($this->XML['id']);
		$this->TABLE_USERS = sprintf('%s_users', $this->ID);
		$this->TABLE_ENTRIES = sprintf('%s_entries', $this->ID);
		$this->TABLE_TEXTS = sprintf('%s_texts', $this->ID);
		$this->TABLE_TEXT_ENTRIES = sprintf('%s_text_entries', $this->ID);
	}

	private function init_tables()
	{
		$query = sprintf('CREATE TABLE IF NOT EXISTS `%s` (
  					`id` int(11) NOT NULL auto_increment,
  					`email` varchar(100) default NULL,
  					`date` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  					PRIMARY KEY  (`id`),
  					UNIQUE KEY `email` (`email`)
					) ENGINE=InnoDB  DEFAULT CHARSET=utf8;', $this->TABLE_USERS);
		$DB->execute($query);

		$query = sprintf('CREATE TABLE IF NOT EXISTS `%s` (
  					`id` int(11) NOT NULL auto_increment,
  					`item_id` int(11) NOT NULL,
  					`value` int(11) NOT NULL,
  					`user` int(11) NOT NULL,
  					PRIMARY KEY  (`id`),
  					UNIQUE KEY `item_id` (`item_id`,`value`,`user`),
  					KEY `user` (`user`)
					) ENGINE=InnoDB  DEFAULT CHARSET=utf8;', $this->TABLE_ENTRIES);
		$DB->execute($query);
		$query = sprintf('ALTER TABLE `%s`
  							ADD CONSTRAINT `%s_ibfk_1` FOREIGN KEY (`user`)
  							REFERENCES `%s` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;',
		$this->TABLE_ENTRIES, $this->TABLE_ENTRIES, $this->TABLE_USERS);
		$DB->execute($query);

		$query = sprintf('CREATE TABLE IF NOT EXISTS `%s` (
  							`id` int(11) NOT NULL auto_increment,
  							`text` varchar(255) NOT NULL,
  							PRIMARY KEY  (`id`),
  							UNIQUE KEY `text` (`text`)
							) ENGINE=MyISAM  DEFAULT CHARSET=utf8;', $this->TABLE_TEXTS);
		$DB->execute($query);

		$query = sprintf('CREATE TABLE IF NOT EXISTS `%s` (
  							`id` int(11) NOT NULL,
  							`text` int(11) NOT NULL,
  							PRIMARY KEY  (`id`,`text`)
							) ENGINE=InnoDB DEFAULT CHARSET=utf8;', $this->TABLE_TEXT_ENTRIES);
		$DB->execute($query);
		$query = sprintf('ALTER TABLE `%s`
  							ADD CONSTRAINT `%s_ibfk_1` FOREIGN KEY (`id`)
  							REFERENCES `%s` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;',
		$this->TABLE_TEXT_ENTRIES, $this->TABLE_TEXT_ENTRIES, $this->TABLE_ENTRIES);
		$DB->execute($query);
	}

	private function get_occurencies($option, $item_id) {
		if($option['type'] == 'text') {
			$occurencies = (int) $option['occurencies'];
			if(!$occurencies) $occurencies = 1;
			$this->pollHTML .= '<dl style="display: none">';
			for($i=0;$i<$occurencies;$i++) {
				$name = sprintf('text_%d_%d', $item_id, $option['id']);
				$this->pollHTML .= sprintf('<dd><input type="text" name="%s[]" value=""/></dd>', $name);
			}
			$this->pollHTML .= '</dl>';
		}
	}

	private function get_radio($item)
	{
		$name = sprintf('item_%d', $item['id']);
		foreach($item->values->children() as $option) {
			$this->pollHTML .= '<dd>';
			$id = sprintf('id_for_%s_%d', $name, $option['id']);
			$this->pollHTML .= sprintf('<input type="radio" id="%s" value="%d" name="%s"/>', $id, $option['id'], $name);
			$this->pollHTML .= sprintf('<label for="%s">%s</label>', $id, trim($option));
			$this->get_occurencies($option, $item['id']);
			$this->pollHTML .= '</dd>';
		}
	}

	private function get_checkbox($item)
	{
		$name = sprintf('item_%d', $item['id']);
		$values = $item->values;
		if($values) {
			foreach($values->children() as $option) {
				$this->pollHTML .= '<dd>';
				$id = sprintf('id_for_%s_%d', $name, $option['id']);
				$this->pollHTML .= sprintf('<input type="checkbox" id="%s" value="%d" name="%s[]"/>', $id, $option['id'], $name);
				$this->pollHTML .= sprintf('<label for="%s">%s</label>', $id, trim($option));
				$this->get_occurencies($option, $item['id']);
				$this->pollHTML .= '</dd>';
			}
		}
		else {
			$this->pollHTML .= '<dd class="standalone">';
			$id = sprintf('id_for_%s', $name);
			$this->pollHTML .= sprintf('<input type="checkbox" id="%s" value="1" name="%s[]"/>', $id, $name);
			$this->pollHTML .= '</dd>';
		}
	}

	private function get_select($item)
	{
		$name = sprintf('item_%d', $item['id']);
		$this->pollHTML .= '<dd>';
		$this->pollHTML .= sprintf('<select name="%s">', $name);
		foreach($item->values->children() as $option) {
			$this->pollHTML .= sprintf('<option value="%d">%s</option>', $option['id'], trim($option));
		}
		$this->pollHTML .= '</select>';
		$this->pollHTML .= '</dd>';
	}

	private function get_text($item)
	{
		$name = sprintf('item_%d', $item['id']);
		$this->pollHTML .= '<dd>';
		$this->pollHTML .= sprintf('<input type="text" value="" name="%s"/>', $name);
		$this->pollHTML .= '</dd>';
	}

	private function _process_children($node, $case=NULL) {
		foreach($node->children() as $item) {
			if(!$case && $item['case']) continue;
			if($case && trim($item['case']) != $case) continue;
			switch($item->getName()) {
				case 'page':
					$this->pollHTML .= sprintf('<div class="poll_page" %s>', ($this->page == 1) ? "" : 'style="display: none"');
					$this->_process_children($item);
					$this->pollHTML .= '</div>';
					$this->page++;
					break;

				case 'pageTitle':
					$this->pollHTML .= sprintf('<h4>%s</h4>', trim($item));
					break;

				case 'group':
					$this->pollHTML .= sprintf('<dt>%s</dt><dd><dl>', trim($item->name));
					$this->_process_children($item);
					$this->pollHTML .= '</dl></dd>';
					break;

				case 'item':
					$this->pollHTML .= sprintf('<dt class="question" id="%s">%s</dt>', sprintf('item_%d', $item['id']), trim($item->name));
					switch($item['type']) {
						case 'checkbox':
							$this->get_checkbox($item);
							break;

						case 'select':
							$this->get_select($item);
							break;

						case 'text':
							$this->get_text($item);
							break;

						case 'textarea':
							break;

						default:
							$this->get_radio($item);
							break;
					}
					break;
			}
		}
	}

	public function get_poll_map()
	{
		$map = array();
		foreach($this->XML->xpath('//item') as $item) {
			$map_item = array();
			$item_id = (int) $item['id'];
			$item_name = trim($item->name);
			$parent = $this->get_item('..', $item);
			if($parent->getName() == 'group') {
				$item_name = sprintf('%s %s', trim($parent->name), $item_name);
			}
			$map_item['id'] = $item_id;
			$map_item['name'] = $item_name;
			$map_item_values = array();
			$values = $this->get_item('values', $item);
			if($values) {
				foreach($values->xpath('value') as $value) {
					$map_item_values[(int)$value['id']] = trim($value);
				}
			}
			$map_item['values'] = $map_item_values;
			$map[$item_id] = $map_item;
		}
		return $map;
	}

	private function get_text_entries($entry_id)
	{
		global $DB;
		$query = sprintf("SELECT
					pt.text
				FROM `%s` pte
				JOIN `%s` pt ON pte.text=pt.id
				WHERE pte.id=$entry_id", $this->TABLE_TEXT_ENTRIES, $this->TABLE_TEXTS);
		$data = $DB->fetch($query);
		$result = array();
		foreach($data as $item) {
			$result[] = $item['text'];
		}
		return $result;
	}

	public function get_totals()
	{
		global $DB;

		$total_votes = $DB->fetch_field(sprintf('SELECT COUNT(`id`) as total FROM `%s` WHERE email IS NOT NULL', $this->TABLE_USERS), 'total');
		$total_rejects = $DB->fetch_field(sprintf('SELECT COUNT(`id`) as total FROM `%s` WHERE email IS NULL', $this->TABLE_USERS), 'total');

		return array('votes' => $total_votes, 'rejects' => $total_rejects);
	}

	public function get_chart($data) {
		global $DB;

		$x = (int) $data['x'];
		$y = (int) $data['y'];
		$filters = (array) $data['filters'];

		$selects = array();
		$joins = array();
		$filternames = array();
		$idx = 1;

		$MAP = $this->get_poll_map();

		foreach($filters as $filter) {
			$filter_map_item = $MAP[$y];
			$filternames[] = sprintf('number|%s', $filter_map_item['values'][(int)$filter]);
			$selects[] = sprintf(' COUNT(pe%d.user) `total%d` ', $idx, $idx);
			$joins[] = sprintf(' LEFT JOIN `%s` pe%d ON (pe.user=pe%d.user AND pe%d.item_id=%d AND pe%d.value=%d) ', $this->TABLE_ENTRIES,
			$idx, $idx, $idx, $y, $idx, $filter);
			$idx++;
		}
		$query = sprintf("SELECT pe.value %s FROM `%s` pe %s WHERE pe.item_id=%d GROUP BY pe.value",
		($y) ? ','.implode(',',$selects) : ",COUNT(pe.user) total1 ",
		$this->TABLE_ENTRIES,
		($y) ? implode('', $joins) : "", $x);

		$data = $DB->fetch($query);

		$types = array_merge((array) sprintf('string|%s', $MAP[$x]['name']), $filternames);
		if(!$y) {
			$types[] = 'number|Брой';
		}
		$x_values = $MAP[$x]['values'];
		$chart_data = array();
		foreach($data as $record) {
			$row = array();
			$row[] = $x_values[$record['value']];
			foreach($record as $cname=>$cvalue) {
				if(preg_match('/^total\d+/', $cname)) {
					$row[] = (int) $cvalue;
				}
			}
			$chart_data[] = $row;
		}
		return array('types' => $types, 'data' => $chart_data);
	}

	public function get_results()
	{
		$MAP = $this->get_poll_map();
		global $DB;

		$query = sprintf('SELECT
						pe.id entry_id,
						pu.email,
						pe.item_id id,
						pe.value,
						count( pte.id ) texts
					FROM `%s` pe
					JOIN `%s` pu ON pe.user = pu.id
					LEFT JOIN `%s` pte ON pe.id = pte.id
					GROUP BY pe.id
					ORDER BY pe.item_id', $this->TABLE_ENTRIES, $this->TABLE_USERS, $this->TABLE_TEXT_ENTRIES);
		$data = $DB->fetch($query);
		$results = array();
		foreach($data as $entry) {
			$user = array();
			$user_entry = array();
			if(array_key_exists($entry['email'], $results)) {
				$user = $results[$entry['email']];
				if(array_key_exists($entry['id'], $user)) {
					$user_entry = $user[$entry['id']];
				}
			}
			if($entry['texts'] > 0) {
				$complex_entry = array();
				$complex_entry['value'] = $entry['value'];
				$complex_entry['texts'] = $this->get_text_entries($entry['entry_id']);
				$user_entry[] = $complex_entry;
			}
			else {
				$user_entry[] = $entry['value'];
			}
			$user[$entry['id']] = $user_entry;
			$results[$entry['email']] = $user;
		}
		foreach($results as $user=>$entries) {
			printf('<h2>%s</h2><dl>', $user);
			foreach($entries as $key=>$values) {
				$map_item  = $MAP[$key];
				printf('<dt>%s</dt>', $map_item['name']);
				foreach($values as $value) {
					if(is_array($value)) {
						printf('<dd>%s - %s</dd>', $map_item['values'][$value['value']], implode(';', $value['texts']));
					}
					else {
						printf('<dd>%s</dd>', $map_item['values'][$value]);
					}
				}
			}
			echo '</dl>';
		}
	}


	public function get_poll_html()
	{
		$this->page = 1;
		$this->pollHTML = '<h2 title="Анкета">Анкета<span id="poll_paging">&nbsp;</span></h2><form method="post" action="/template/inquery.php?action=submit" id="pollForm"><dl>';
		$this->_process_children($this->XML);
		if($this->page > 1) {
			$this->pollHTML .= '<dt id="next_poll_page"><a href="#">Следваща страница</a></dt>';
		}

		$this->pollHTML .= '<dt id="email_label" style="display: none">Електронен адрес:</dt><dd id="email_input" style="display: none"><input type="text" id="user_email" name="email_entry"/></dd>';
		$this->pollHTML .= '</dl><p><input type="button" id="submitButton" value="Изпрати" class="btn" style="display: none"/></p></form>';
		return $this->pollHTML;
	}

	public function get_reject_html()
	{
		$this->pollHTML = '<form method="post" action="/template/inquery.php?action=rejecting" id="pollForm"><dl>';
		$this->_process_children($this->XML, 'reject');
		$this->pollHTML .= '</dl><p><input type="button" id="submitButton" value="Изпрати" class="btn"/></p></form>';
		return $this->pollHTML;
	}

	private function get_item($xpath, $root=NULL)
	{

		$result = (!$root) ? $this->XML->xpath($xpath) : $root->xpath($xpath);
		if(!$result) return;
		return $result[0];
	}

	private function resolve_item($id, $valuecode)
	{
		$item = $this->get_item(sprintf("//item[@id='%d']", $id));
		if(!$item) {
			return;
		}
		$name = trim($item->name);
		if(!$name) continue;
		$parent = $this->get_item('..', $item);
		if($parent->getName() == 'group') {
			$name = sprintf('%s %s', $parent->name, $name);
		}
		$valuecodes = array();
		if(!is_array($valuecode)) {
			$valuecodes = array($valuecode);
		}
		else {
			$valuecodes = $valuecode;
		}
		$values = array();
		foreach($valuecodes as $valuecode) {
			$value_item = $this->get_item(sprintf("value[@id='%d']", $valuecode), $item->values);
			if(!$value_item) {
				return;
			}
			$values[(int)$valuecode] = trim((string) $value_item);
		}
		$result = array();
		$result['name'] = $name;
		$result['id'] = $id;
		$result['values'] = $values;
		return $result;
	}

	private function process_submitted_item($id, $valuecode, &$results)
	{
		$resolved = $this->resolve_item($id, $valuecode);
		if(!$resolved) {
			http_error(700, 'Подадени невалидни данни.');
		}
		$results[$id] = array_keys($resolved['values']);
	}

	private function process_submitted_text_item($id, $valueid, $valuecode, &$results)
	{
		foreach($valuecode as $key=>$text) {
			if(!trim($text)) {
				unset($valuecode[$key]);
			}
		}
		if(!count($valuecode)) return;
		$resolved = $this->resolve_item($id, $valueid);
		if(!$resolved) {
			http_error(700, 'Подадени невалидни данни.');
		}
		if(array_key_exists($id, $results) && in_array($valueid, array_keys($resolved['values']))) {
			$idx = array_search($valueid, $results[$id]);
			$results[$id][$idx] = array();
			$results[$id][$idx]['value'] = $valueid;
			$results[$id][$idx]['texts'] = $valuecode;
		}
	}

	private function insert_user($email)
	{
		global $DB;
		$args = array();
		$query = '';
		if(!$email) {
			$query = sprintf('INSERT INTO `%s` SET id=NULL', $this->TABLE_USERS);
		}
		else {
			$args = array('s', $email);
			$query = sprintf('INSERT INTO `%s` SET email=?', $this->TABLE_USERS);
		}
		$st = $DB->execute($query, $args);
		if($st->errno) {
			if($st->errno == 1062) {
				http_error(600, sprintf('Вече е изпратена анкета от този адрес (%s)', $email));
			}
			http_error(702, $st->error);
		}
		return $st->insert_id;
	}

	private function insert_text($text) {
		global $DB;

		$args = array('s', $text);
		$query = sprintf('INSERT INTO `%s` SET `text`=?', $this->TABLE_TEXTS);
		$st = $DB->execute($query, $args);
		if($st->errno == 1062) {
			$query = sprintf('SELECT id FROM `%s` WHERE `text`=?', $this->TABLE_TEXTS);
			return $DB->fetch_field($query, 'id', $args);
		}
		return $st->insert_id;
	}

	private function clean_entries($user_id=0)
	{
		global $DB;

		$user_id = (int) $user_id;
		if(!$user_id) return;
		$query = sprintf('DELETE FROM `%s` WHERE id='.$user_id, $this->TABLE_USERS);
		$DB->execute($query);
	}

	public function save_entry($DATA, $reject=false)
	{
		global $DB;

		$results = array();
		$user_email = '';
		foreach($DATA as $keycode=>$valuecode) {
			list($crap, $id, $valueid) = explode('_', $keycode, 3);
			switch($crap) {
				case "item":
					$this->process_submitted_item($id, $valuecode, $results);
					break;
				case "text":
					$this->process_submitted_text_item($id, $valueid, $valuecode, $results);
					break;
				case "email":
					$user_email = $valuecode;
					break;
				default:
					break;
			}
		}
		if(!$reject && !preg_match('/^[^@]+@[-a-z0-9.]+$/', $user_email)) {
			http_error(700, sprintf('Неправилен емейл адрес. -> %s', $user_email));
		}
		$user_id = $this->insert_user($user_email);
		foreach($results as $id=>$values) {
			foreach($values as $value) {
				if(is_array($value)) {
					$query = sprintf("INSERT INTO `%s` SET `user`=$user_id,`item_id`=$id,`value`=$value[value]", $this->TABLE_ENTRIES);
					$st = $DB->execute($query);
					if($st->errno) {
						$this->clean_entries($user_id);
						http_error(603, 'Системна грешка. Моля опитайте пак след 10 минути.');
					}
					$entry_id = $st->insert_id;
					foreach($value['texts'] as $text) {
						$text_id = $this->insert_text($text);
						$query = sprintf("INSERT INTO `%s` SET id=$entry_id,text=$text_id", $this->TABLE_TEXT_ENTRIES);
						$DB->execute($query);
					}
				}
				else {
					$query = sprintf("INSERT INTO `%s` SET `user`=$user_id,`item_id`=$id,`value`=$value", $this->TABLE_ENTRIES);
					$st = $DB->execute($query);
					if($st->errno) {
						$this->clean_entries($user_id);
						http_error(603, 'Системна грешка. Моля опитайте пак след 10 минути.');
					}
				}
			}
		}
	}
}
