<?php
/**
 * ��ũ�� ī�װ� Ŭ���� v2.0
 *@author: kuorkisi
 *@authDate: 2011.09.05
 *@note: ������
 */

class rankup_category extends rankup_util {

	var $table = 'rankup_category';

	function rankup_category() {
		parent::rankup_util();

	}

	// ī�װ� 1�� ��ȯ
	function get($no, $entry) {
		$datas = $this->query("select * from $this->table where no=$no");
		return fetch_contents($datas, $entry);
	}

	// ��ȯ
	function load($kind, $entry, $parents='', $depth=1) {
		if($parents) $addWhere = " and find_in_set('$_POST[no]', parents)";
		$datas = $this->query("select * from $this->table where kind='$kind'".$addWhere." and depth=$depth order by rank");
		return fetch_contents($datas, $entry);
	}

	// ī�װ� �� ��ȯ
	function get_item($no) {
		$rows = $this->queryFetch("select * from $this->table where no=$no");
		return $rows['item'];
	}

	// ���� ��ȯ
	function get_bundles($kind, $bundle) {
		return $this->query("select * from $this->table where kind='$kind' and bundle='$bundle' order by rank");
	}

	// ���� ����
	function del_bundles($kind, $bundles) {
		return $this->query("delete from $this->table where kind='$kind' and bundle in($bundles)");
	}

	// �̱�ī�װ� ��ȯ
	function print_single_categories($kind, $entry, $bundle='') {
		if($bundle) $where = q(" and bundle='%s'", $bundle);
		$datas = $this->query("select * from $this->table where kind='$kind'".$where." order by rank");
		return fetch_contents($datas, $entry, array($this, '_c25'));
	}
	function _c25($bind) {
		extract($bind);
		if($rows['value']) {
			$values = @unserialize($rows['value']);
			if(is_array($values)) $rows = array_merge($rows, $values);
		}
		if(isset($value) && $rows['no']==$value) $rows['on_select'] = $on_select;
		$rows['cost_format'] = number_format($rows['cost']);
		return array($rows, $skin);
	}

	// �̱� ī�װ� values ��ȯ
	function get_single_values($infos, $index) {
		// ���ʿ� �� ����
		$_vals = array();
		foreach(explode(',', $infos['extra_value']) as $item) {
			list($key, $field) = explode(':', $item);
			$_vals[$key] = $infos[$field][$index];
		}
		return count($_vals) ? serialize($_vals) : '';
	}

	// ����
	function save() {
		if($_POST['type']=='single') { // �̱��� - �迭�� �Ѿ��
			$identities = array();
			if(count($_POST['items'])) {
				foreach($_POST['items'] as $index=>$item) {
					$_vals['kind'] = $_POST['kind'];
					$_vals['bundle'] = $_POST['bundle'];
					$_vals['item'] = $item;
					$_vals['note'] = $_POST['notes'][$index];
					if($_POST['extra_value']) $_vals['value'] = $this->get_single_values($_POST, $index); // string
					$_vals['rank'] = $index + 1;
					$no = $_POST['nos'][$index];
					$values = $this->change_query_string($_vals);
					if($no) $this->query("update $this->table set $values where no=$no");
					else {
						$this->query("insert $this->table set $values");
						$id = $_POST['identity'][$index];
						$identities[$id] = mysql_insert_id();
					}
				}
			}
			if(count($identities)) return $identities;
		}
		else { // ��Ƽ�� - �Ϲ����� ī�װ� ����
			if($_POST['depth']) { // �߰���
				$_vals['kind'] = $_POST['kind'];
				$_vals['bundle'] = $_POST['bundle'];
				$_vals['depth'] = $_POST['depth'];
				if($_POST['no']) $_vals['parents'] = $_POST['no'];
				$addWhere = $_POST['no'] ? " and parents='$_POST[no]'" : " and parents is null";
				$rank = $this->queryR("select max(rank) from $this->table where kind='$_POST[kind]' and depth='$_POST[depth]'".$addWhere);
				$_vals['rank'] = $rank+1;
				if($_POST['depth']>1) {
					$parent = array_pop(explode(',', $_POST['no']));
					$this->query("update $this->table set has_child='yes' where no=$parent"); // has_child update
				}
			}
			$_vals['item'] = $_POST['item'];
			$_vals['used'] = $_POST['used'];
			$values = $this->change_query_string($_vals);
			if($_POST['depth']) {
				$this->query("insert $this->table set $values");
				$_POST['no'] = mysql_insert_id();
			}
			else $this->query("update $this->table set $values where no=$_POST[no]");
		}
	}

	// ���� ���� - 2012.04.10 fixed
	function set_rank() {
		$rows = $this->queryFetch("SELECT * FROM $this->table WHERE no=$_POST[no]"); // �ҽ�
		$trows = $this->queryFetch("SELECT * FROM $this->table WHERE no=$_POST[tno]"); // Ÿ��
		$addWhere = $rows['parents'] ? q(" AND parents='%s'", $rows['parents']) : ' AND parents is null';
		if($rows['rank']>$trows['rank']) $this->query("UPDATE $this->table SET rank=rank+1".q(" WHERE kind='%s' AND rank<%d AND rank>=%d", $rows['kind'], $rows['rank'], $trows['rank']).$addWhere);
		else $this->query("UPDATE $this->table SET rank=rank-1".q(" WHERE kind='%s' AND rank>%d AND rank<=%d", $rows['kind'], $rows['rank'], $trows['rank']).$addWhere);
		$this->query("UPDATE $this->table SET rank=$trows[rank] WHERE no=$rows[no]");
	}

	// ����
	function del() {
		if($_POST['type']=='single') { // �̱���
			$nos = str_replace('__', ',', $_POST['nos']);
			$this->query("delete from $this->table where no in($nos)");
		}
		else { // ��Ƽ��
			if($_POST['nos']) $nos = explode('__', $_POST['nos']);
			foreach($nos as $no) {
				$rows = $this->queryFetch("select parents from $this->table where no=$no");
				$this->query("delete from $this->table where no=$no or find_in_set('$no', parents)");
				if($rows['parents']) {
					$childs = $this->queryR("select count(no) from $this->table where parents='$rows[parents]'");
					if(!$childs) {
						$parent = array_pop(explode(',', $rows['parents']));
						$this->query("update $this->table set has_child='no' where no=$parent and has_child='yes'");
					}
				}
			}
		}
	}

	// ī�װ� ��� ��ȯ
	function print_contents($kind, $parent='', $entry) {
		$where = $parent ? " and find_in_set('$parent', parents)" : " and parents is null";
		$datas = $this->query("select * from $this->table where kind='$kind'".$where." order by rank");
		return fetch_contents($datas, $entry, array($this, '_c140'));
	}
	function _c140($bind) {
		extract($bind);
		if(isset($on_selected) && $value && $value==$rows['no']) $rows['on_selected'] = $on_selected;
		return array($rows, $skin);
	}
}
?>
