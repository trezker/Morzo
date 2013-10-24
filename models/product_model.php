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
			select C.ID, C.Name, PC.Food_nutrition, PC.Container_mass_limit, PC.Container_volume_limit from Product_category PC
			join Category C on C.ID = PC.Category_ID
			 where Product_ID = ?
			', array($product_id));

		if(!$rs) {
			return false;
		}
		
		$r = array('product' => $rs->fields, 'categories' => $rs2->GetArray());
		return $r;
	}

	public function Save_product($product) {
		$db = Load_database();
		
		$product_id = $product['id'];
		
		$db->StartTrans();

		if($product_id == -1) {
			$args = array(	$product['name'], 
							$product['mass'],
							$product['volume'],
							$product['rot_rate']
						);

			$rs = $db->Execute('
				insert into Product (Name, Mass, Volume, Rot_rate) values (?, ?, ?, ?)
				', $args);
			
			$product_id = $db->Insert_ID();
		} else {
			$args = array(	$product['name'], 
							$product['mass'],
							$product['volume'],
							$product['rot_rate'],
							$product_id
						);

			$rs = $db->Execute('
				update Product set Name = ?, Mass = ?, Volume = ?, Rot_rate = ? where ID = ?
				', $args);
		}

		if(isset($product['categories'])) {
			foreach($product['categories'] as $category) {
				if(isset($category['state']) && $category['state'] == 'remove')
					$this->Remove_product_category($product_id, $category['id']);
				else
					$this->Add_product_category($product_id, $category);
			}
		}

		$success = !$db->HasFailedTrans();
		$db->CompleteTrans();
		if($success != true)
			return false;

		return true;
	}
	
	public function Add_product_category($product_id, $category) {
		if(!isset($category['nutrition']) || !is_numeric($category['nutrition']))
			$category['nutrition'] = NULL;
		if(!isset($category['mass_limit']) || !is_numeric($category['mass_limit']))
			$category['mass_limit'] = NULL;
		if(!isset($category['volume_limit']) || !is_numeric($category['volume_limit']))
			$category['volume_limit'] = NULL;
		$db = Load_database();
		$query = 'insert into Product_category(Product_id, Category_ID, Food_nutrition, Container_mass_limit, Container_volume_limit)
		values(?, ?, ?, ?, ?) on duplicate key update Food_nutrition = ?, Container_mass_limit = ?, Container_volume_limit = ?';
		$array = array($product_id, 
						$category['id'], 
						$category['nutrition'], 
						$category['mass_limit'], 
						$category['volume_limit'], 
						$category['nutrition'], 
						$category['mass_limit'], 
						$category['volume_limit']);
		$rs = $db->Execute($query, $array);
		return $rs;
	}

	public function Remove_product_category($product_id, $category_id) {
		$db = Load_database();
		$query = 'delete from Product_category where Product_ID = ? and Category_ID = ?';
		$array = array($product_id, $category_id);
		$rs = $db->Execute($query, $array);
		return $rs;
	}
}
?>
