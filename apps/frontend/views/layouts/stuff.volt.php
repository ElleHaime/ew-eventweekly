<div id="fb-root" display="none;"></div>

<div style="display:none;" id="current_location" latitude="<?php echo $location->latitude; ?>" longitude="<?php echo $location->longitude; ?>"></div>

<input type="hidden" id="popupRedirect" value="">

<?php if (isset($flashMsgText)) { ?>
	<div style="display:none;" id="splash_messages" flashMsgText="<?php echo $flashMsgText; ?>" flashMsgType="<?php echo $flashMsgType; ?>"></div>
<?php } ?>

<?php if (isset($location_conflict)) { ?>
	<div style="display:none;" id="conflict_location" location_conflict="<?php echo $location_conflict; ?>"></div>
<?php } ?>

<?php if (isset($external_logged)) { ?>
    <div id="external_logged" extname="<?php echo $external_logged; ?>" display="none;"></div>
<?php } ?>

<?php if (isset($permission_base)) { ?>
	<input type="hidden" id="permission_base" value="<?php echo $permission_base; ?>">
<?php } else { ?>
	<input type="hidden" id="permission_base" values = "0">
<?php } ?>

<?php if (isset($permission_publish)) { ?>
    <input type="hidden" id="permission_publish" value="<?php echo $permission_publish; ?>">
<?php } else { ?>
	<input type="hidden" id="permission_publish" value="0">
<?php } ?>

<?php if (isset($permission_manage)) { ?>
    <input type="hidden" id="permission_manage" value="<?php echo $permission_manage; ?>">
<?php } else { ?>
	<input type="hidden" id="permission_manage" value="0">
<?php } ?>

<?php if (isset($acc_external)) { ?>
    <input type="hidden" id="member_ext_uid" value="<?php echo $acc_external->account_uid; ?>">
<?php } ?>

<?php if (isset($acc_synced)) { ?>
    <input type="hidden" id="acc_synced" value="1">
<?php } ?>

<?php if (isset($member->id)) { ?>
    <input id="isLogged" type="hidden" value="1" />
<?php } else { ?>
    <input id="isLogged" type="hidden" value="0" />
<?php } ?>

<?php if (isset($isMobile)) { ?>
    <input id="isMobile" type="hidden" value="<?php echo $isMobile; ?>" />
<?php } ?>

<?php if (isset($fbAppId)) { ?>
    <input id="fbAppId" type="hidden" value="<?php echo $fbAppId; ?>" />
<?php } ?>

<?php if (isset($fbAppSecret)) { ?>
    <input id="fbAppSecret" type="hidden" value="<?php echo $fbAppSecret; ?>" />
<?php } ?>