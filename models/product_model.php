<?php

require_once '../models/database.php';

class Product_model {
	public function Get_products() {
		$db = Load_database();
		
		$rs = $db->Execute('
			select ID, Name from Product
			', array());

		if(!$rs) {
			return false;
		}
		
		$products = array();
		foreach ($rs as $row) {
    		$products[] = $row;
		}
		return $products;
	}

	public function Get_product($product_id) {
		if($product_id == -1) {
			return false;
		}
		
		$db = Load_database();
		
		$rs = $db->Execute('
			select ID, Name, Mass, Volume, Rot_rate from Product where ID = ?
			', array($product_id));

		if(!$rs || $rs->RecordCount() == 0) {
			return false;
		}
		
		$rs2 = $db->Execute('
			select PC.Category_ID, C.Name from Product_category PC
			join Category C on C.ID = PC.Category_ID
			 where Product_ID = ?
			', array($product_id));
		
		$r = array('product' => $rs->fields, 'categories' => $rs2->GetArray());
		return $r;
	}

	public function Save_product($product) {
		$db = Load_database();
		
		if($product['id'] == -1) {
			$args = array(	$product['name'], 
							$product['mass'],
							$product['volume'],
							$product['rot_rate']
						);

			$rs = $db->Execute('
				insert into Product (Name, Mass, Volume, Rot_rate) values (?, ?, ?, ?)
				', $args);
		} else {
			$args = array(	$product['name'], 
							$product['mass'],
							$product['volume'],
							$product['rot_rate'],
							$product['id']
						);

			$rs = $db->Execute('
				update Product set Name = ?, Mass = ?, Volume = ?, Rot_rate = ? where ID = ?
				', $args);
		}

		if(!$rs) {
			echo $db->ErrorMsg();
			return false;
		}

		return true;
	}
}
?>
