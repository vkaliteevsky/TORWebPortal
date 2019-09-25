<?
function isStatusExecuted($statusId) {
    return (($statusId == 4) || ($statusId == 5) || ($statusId == 6));
}
function toShowDevice($roleId, $statusId) {
	return True;
}
function toShowCompany($roleId, $statusId) {
	if ($roleId != 1) return True;
	else return False;
}
function toShowAddress($roleId, $statusId) {
    if ($roleId != 1) return True;
    else return False;
}
function toShowStatus($roleId, $statusId) {
	return True;
}
function toShowPriority($roleId, $statusId) {
	return True;
}
function toShowClientText($roleId, $statusId) {
	return True;
}
function toShowEngineerComment($roleId, $statusId) {
	return True;
}
function toShowContactPerson($roleId, $statusId) {
    if ($roleId >= 2) return True;
    return False;
}
function toShowInitiator($roleId, $statusId) {
    return true;
}
function toShowContinueWork($roleId, $statusId) {
	//if ($roleId == 1) return False;
	//if ($statusId == 1) return False;
	return False;
}
function toShowEngineer($roleId, $statusId) {
	return True;
}
function toShowLinkedOrders($roleId, $statusId) {
	return ($roleId != 1);
}
function toShowManagerComments($roleId, $statusId) {
	return ($roleId != 1);
}
function toShowDates($roleId, $statusId) {
	return ($roleId != 1);
}
function toShowDeleteButton($roleId, $statusId) {
	//return ($roleId == 3) or ($roleId == 4);
	return False;
}
function toShowArrival($roleId, $statusId) {
	return True;
}
function toShowDefaultEngineer($roleId, $statusId) {
    return  ($roleId >= 3);
}
function toShowCancelOrderButton($roleId, $statusId) {
    if (($roleId == 1) and ($statusId > 1)) return False;
    if ($roleId == 2) return False;
    return True;
}

function isDeviceEnabled($roleId, $statusId, $isActiveEngineer) {
	if ($roleId == 2) return False;
//    if (($roleId == 1) and (isStatusExecuted($statusId))) return False;
    if ($roleId == 1) return False;
	return True;
}
function isStatusEnabled($roleId, $statusId, $isActiveEngineer) {
	if ($roleId == 1) return False;
	if ($roleId == 4) return False;	// project manager
	if (($roleId == 3) or ($roleId == 5)) return True;
	if ($roleId == 2) {
		return $isActiveEngineer === True;
	}
}
function isCounterEnabled($roleId, $statusId) {
    if (($roleId == 1) and (isStatusExecuted($statusId))) return False;
    return True;
}
function isPriorityEnabled($roleId, $statusId, $isActiveEngineer) {
	if ($roleId == 2) return False;
	if (($roleId == 1) and (isStatusExecuted($statusId))) return False;
	return True;
}
function isClientTextEnabled($roleId, $statusId, $isActiveEngineer) {
	//if ($roleId != 1) return False;
	if ($roleId == 2) return False;
    if (($roleId == 1) and (isStatusExecuted($statusId))) return False;
	return True;
}
function CanModifyStorageLeftAmount($roleId) {
    if (($roleId == 1) || ($roleId == 5)) return true;
    return false;
}
function isEngineerCommentEnabled($roleId, $statusId, $isActiveEngineer) {
	if ($roleId == 1) return False;
	if ($roleId == 2) {
	    if ($isActiveEngineer) return true;
	    else return false;
    }
	return True;
}
function isManagerCommentEnabled($roleId, $statusId, $isActiveEngineer) {
    if ($roleId == 1) return False;
    return True;
}
function isContinueWorkEnabled($roleId, $statusId, $isActiveEngineer) {
	return True;
}
function isEngineerEnabled($roleId, $statusId, $isActiveEngineer) {
	if ($roleId == 1) return False;
	if ($roleId == 2) return False;
	return True;
}
function isManagerCommentsEnabled($roleId, $statusId, $isActiveEngineer) {
	if ($roleId == 1) return false;
	if ($roleId == 2) {
	    if ($isActiveEngineer) return true;
	    else return false;
    }
	return true;
}
function canDeleteOrders($roleId, $statusId) {
    return ($roleId == 5);
}
function isArrivalEnabled($roleId, $statusId, $isActiveEngineer) {
	if ($roleId == 1) return False;
	if ($roleId == 2) {
	    if (!$isActiveEngineer) return false;
		if (($statusId == 2) or ($statusId == 3)) return True;
		else return False;
	} else {
		return True;
	}
	return True;
}


// Зона Вовы
function toShowDataPriezda($roleId) {
	return ($roleId != 1);
}
function toChangeCounter($roleId, $statusId) {
    if (($roleId == 1) and (isStatusExecuted($statusId))) return False;
    return True;
}

function toShowEngineerOption($roleId) {
    if ($roleId == 3) return True;
    else return False;
}
function toShowPlace($roleId, $statusId) {
    if ($roleId == 1) return False;
    else return True;
}

/*
 * Отвечает на вопрос: "В какие статусы пользователь с ролью $roleId может перевести заявку $statusId?"
 */
function mapPossibleStatuses($roleId, $statusId) {
    $arr = array();
    if ($roleId == 1) {  // клиент
        $arr = array();
    } else if ($roleId == 2) {  // инженер
//        if ($statusId == 2) $arr = array(2, 3);
//        else if ($statusId == 3) $arr = array(3, 4, 5);
//        else $arr = array(4, 5);
        if ($statusId == 2) $arr = array(1, 2, 3, 4, 5);
        else if ($statusId == 3) $arr = array(1, 2, 3, 4, 5);
        else $arr = array(1, 2, 3, 4, 5);
    } else if ($roleId == 3) {
//        $arr = array(2, 3, 4, 5);
        $arr = array(1, 2, 3, 4, 5);
    } else if ($roleId == 4) {
//        $arr = array(2, 3, 4, 5);
        $arr = array(1, 2, 3, 4, 5);
    } else if ($roleId == 5) {
        $arr = array(1, 2, 3, 4, 5);
    } else if ($roleId == 6) {
        $arr = array(1, 2, 3, 4, 5);
    } else {
        handleError(1, null);
        return array($statusId);
    }
    $arr[] = $statusId;
    $res = array_unique($arr);
    sort($res);
    return $res;
}
?>