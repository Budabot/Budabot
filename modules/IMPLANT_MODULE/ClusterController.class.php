<?php

namespace Budabot\User\Modules;

use Exception;

/**
 * Authors: 
 *  - Tyrence (RK2)
 *
 * @Instance
 *
 * Commands this class contains:
 *	@DefineCommand(
 *		command     = 'cluster',
 *		accessLevel = 'all',
 *		description = 'Find which clusters buff a specified skill',
 *		help        = 'cluster.txt'
 *	)
 */
class ClusterController {

	/**
	 * Name of the module.
	 * Set automatically by module loader.
	 */
	public $moduleName;
	
	/** @Inject */
	public $db;

	/** @Inject */
	public $text;

	/** @Inject */
	public $util;

	/**
	 * @HandlesCommand("cluster")
	 * @Matches("/^cluster$/i")
	 */
	public function clusterListCommand($message, $channel, $sender, $sendto, $args) {
		$sql = "SELECT ClusterID, LongName FROM Cluster ORDER BY LongName ASC";
		$data = $this->db->query($sql);
		$count = count($data);

		forEach ($data as $cluster) {
			$blob .= $this->text->makeChatcmd($cluster->LongName, "/tell <myname> cluster $cluster->LongName") . "\n";

		}
		$msg = $this->text->makeBlob("Cluster List ($count)", $blob);
		$sendto->reply($msg);
	}
	
	/**
	 * @HandlesCommand("cluster")
	 * @Matches("/^cluster (.+)$/i")
	 */
	public function clusterCommand($message, $channel, $sender, $sendto, $args) {
		$search = trim($args[1]);
		
		list($query, $params) = $this->util->generateQueryFromParams(explode(' ', $search), 'LongName');

		$sql = "SELECT ClusterID, LongName FROM Cluster WHERE $query";
		$data = $this->db->query($sql, $params);
		$count = count($data);

		if ($count == 0) {
			$msg = "No skills found that match <highlight>$search<end>.";
		} else {
			$implantDesignerLink = $this->text->makeChatcmd("implant designer", "/tell <myname> implantdesigner");
			$blob = "Click 'Add' to add cluster to $implantDesignerLink.\n\n";
			forEach ($data as $cluster) {
				$sql = "SELECT i.ShortName as Slot, c2.Name AS ClusterType FROM ClusterImplantMap c1 JOIN ClusterType c2 ON c1.ClusterTypeID = c2.ClusterTypeID JOIN ImplantType i ON c1.ImplantTypeID = i.ImplantTypeID WHERE c1.ClusterID = ? ORDER BY c2.ClusterTypeID DESC";
				$results = $this->db->query($sql, $cluster->ClusterID);
				
				$blob .= "<pagebreak><highlight>$cluster->LongName<end>:\n<tab>";

				forEach ($results as $row) {
					$impDesignerLink = $this->text->makeChatcmd("Add", "/tell <myname> implantdesigner $row->Slot $row->ClusterType $cluster->LongName");
					$clusterType = ucfirst($row->ClusterType);
					$blob .= "<font color=#ffcc33>$clusterType</font>: $row->Slot ($impDesignerLink)<tab>";
				}
				$blob .= "\n\n";
			}
			$msg = $this->text->makeBlob("Cluster search results ($count)", $blob);
		}
		$sendto->reply($msg);
	}
}
