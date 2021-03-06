<?php
/*
############################################################################
#  This file is part of OurBank.
############################################################################
#  OurBank is free software: you can redistribute it and/or modify
#  it under the terms of the GNU Affero General Public License as
#  published by the Free Software Foundation, either version 3 of the
#  License, or (at your option) any later version.
############################################################################
#  This program is distributed in the hope that it will be useful,
#  but WITHOUT ANY WARRANTY; without even the implied warranty of
#  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#  GNU Affero General Public License for more details.
############################################################################
#  You should have received a copy of the GNU Affero General Public License
#  along with this program.  If not, see <http://www.gnu.org/licenses/>.
############################################################################
*/
?>

<?php
class Management_Model_Institution extends Zend_Db_Table {
	protected $_name = 'ourbank_institutionaddress';

	public function fetchAllinstitutiondetails() {
		$select = $this->select()
			->setIntegrityCheck(false)  
			->join(array('p' => 'ourbank_institutionaddress'),array('institution_id'))
			->where('p.recordstatus_id = 3 or p.recordstatus_id = 1');
		$result = $this->fetchAll($select);
		return $result->toArray();
	}

	public function getUpdatesinformation($post = array()) {
		$select = $this->select()
			->setIntegrityCheck(false)  
			->join(array('a' => 'ourbank_institutionupdates'),array('institutionupdate_id'))
			->join(array('b' => 'ourbank_userloginupdates'),'a.modified_by =b.user_id')
			->where('b.recordstatus_id = 1 or b.recordstatus_id = 3');
		$result = $this->fetchAll($select);
		return $result->toArray();
	}

	public function SearchInstitution($post = array()) {
		$select = $this->select()
			->setIntegrityCheck(false)  
			->join(array('b' => 'ourbank_institutionaddress'),array('institution_id'))
			->where('b.recordstatus_id = 3 OR b.recordstatus_id = 1')
			->where('b.institutionname like "%" ? "%"',$post['field2'])
			->where('b.institutionshortname like "%" ? "%"',$post['field3'])
			->where('b.city like "%" ? "%"',$post['field4'])
			->where('b.state like "%" ? "%"',$post['field6']);
		$result = $this->fetchAll($select);
		return $result->toArray();
	}

	public function viewinstitution($institution_id) {
		$select = $this->select()
			->setIntegrityCheck(false)  
			->join(array('a' => 'ourbank_institutionaddress'),array('institution_id'))
			->where('a.institution_id = ?',$institution_id)
			->where('a.recordstatus_id = 3 or a.recordstatus_id = 1');
		$result = $this->fetchAll($select);
		return $result->toArray();
//  		die($select->__toString($select));
	}


	public function addinstitution($post,$institution_id) {
		$sessionName = new Zend_Session_Namespace('ourbank');
		$createdby = $sessionName->primaryuserid;
		$data = array('institutionaddress_id'=> '',
					'institution_id'=> $institution_id,
					'recordstatus_id'=>3,
					'institutionname'=>$post['institutionname'],
					'institutionshortname'=>$post['institutionshortname'],
					'institutiondescription'=>$post['institutiondescription'],
					'address1'=>$post['address1'],
					'address2'=>$post['address2'],
					'address3'=>$post['address3'],
					'city'=>$post['city'],
					'state'=>$post['state'],
					'country'=>$post['country'],
					'pincode'=>$post['pincode'],
					'phone'=>$post['phone'],
					'fax'=>$post['fax'],
					'email_Id'=>$post['email_Id'],
					'contactperson'=>$post['contactperson'],
					'contactperson_phone1'=>$post['contactperson_phone1'],
					'contactperson_phone2'=>$post['contactperson_phone2'],
					'contactperson_email'=>$post['contactperson_email'],
					'createdby'=>$createdby,
					'createddate'=>date("Y-m-d"));
		$this->insert($data);
	}

	public function deleteInstitution($institution_id,$remarks) {
		$data = array('recordstatus_id'=> 5,'remarks'=>$remarks);
		$where = 'institution_id = '.$institution_id;
		$this->update($data , $where );
	}

	public function institutionUpdate($updateOldinstitution = array(),$updateNewinstitution = array(),$createdby,$institution_id)
	{
		$this->db = Zend_Db_Table::getDefaultAdapter();
		$match = array();
		foreach ($updateOldinstitution as $key=>$val) {
			if ($val != $updateNewinstitution[$key]) {                           /** if the values are changed */
				$match[] = $key;
			}
		}
		if(count($match) <= 0){
		} else {
			foreach($match as $institution) {
				$tableName ='ourbank_institutionaddress';
				$institutionUpdates = array('institutionupdate_id'=>'',
										'institution_id' => $institution_id,
										'table_name'=>$tableName,
										'filedname'=>$institution,
										'previous_data'=>$updateOldinstitution[$institution],
										'current_data'=>$updateNewinstitution[$institution],
										'modified_by'=>$createdby,
										'modified_date'=>date("Y-m-d"));
				$this->db->insert('ourbank_institutionupdates',$institutionUpdates);
			}
		}
	}

	public function editinstitution($post,$institution_id) {
		$sessionName = new Zend_Session_Namespace('ourbank');
		$createdby = $sessionName->primaryuserid;
		$where = 'institution_id = '.$institution_id;
		$data = array('institutionname'=>$post['institutionname'],
					'institutionshortname'=>$post['institutionshortname'],
					'institutiondescription'=>$post['institutiondescription'],
					'address1'=>$post['address1'],
					'address2'=>$post['address2'],
					'address3'=>$post['address3'],
					'city'=>$post['city'],
					'state'=>$post['state'],
					'country'=>$post['country'],
					'pincode'=>$post['pincode'],
					'phone'=>$post['phone'],
					'fax'=>$post['fax'],
					'email_Id'=>$post['email_Id'],
					'contactperson'=>$post['contactperson'],
					'contactperson_phone1'=>$post['contactperson_phone1'],
					'contactperson_phone2'=>$post['contactperson_phone2'],
					'contactperson_email'=>$post['contactperson_email'],
					'createdby'=>$createdby,
					'createddate'=>date("Y-m-d"));
		$this->update($data,$where );
	}

	public function insertinstitutionId($input = array())
	{
        $this->db = Zend_Db_Table::getDefaultAdapter();
		$this->db->insert("ourbank_institution",$input);
		$result = $this->db->lastInsertId('ourbank_institution');
		return $result;
	}


// 	public function getUpdatesinformation($post = array()) {
// 		$select = $this->select()
// 			->setIntegrityCheck(false)  
// 			->join(array('a' => 'ourbank_productupdates'),array('productupdates_id'))
// 			->join(array('b' => 'ourbank_userloginupdates'),'a.modified_by =b.user_id')
// 			->where('b.recordstatus_id = 1 or b.recordstatus_id = 3');
// 		$result = $this->fetchAll($select);
// 		return $result->toArray();
// 	}
// 

// 
// 	public function addExtendedproduct($input) {
// 		$db = $this->getAdapter();
// 		$db->insert("ourbank_Product_extended",$input);
// 		return ;
// 	}
}
