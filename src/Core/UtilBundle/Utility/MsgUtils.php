<?php

namespace UtilBundle\Utility;

/**
 * Description of MsgUtils
 * @author Phuc Duong
 */
class MsgUtils
{
    public static $messageContent = array(

        'msgRequiredField'          => '%s cannot be blank.',
        'msgWrongFormatNumber'      => '%s must be a number.',
        'msgGreaterThan'            => '%s must be greater than %s.',
        'msgLessThan'               => '%s must be less than %s.',
        'msgWrongRange'             => '%s must be in the range %s to %s.',
        'msgDoesntExist'            => '%s does not exist.',
        'msgAllowFileSizeUploaded'  => 'Size of %s must be less than or equal %s.',
        'msgAllowFilesUploaded'     => 'Extension of image file must be jpg, jpeg, gif, png.',
        'msgWrongMaxLength'         => 'Length of %s must be less than %s characters.',
        'msgWrongLength'            => 'Length of %s must be equal to %s characters.',
        'msgGreaterThanOrEqual'     => "%s must be greater than or equal to %s.",
        'msgMustBeIntergerNumber'   => "%s must be a positive integer.",
        'msgLessThanOrEqualTo'      => '%s must be less than or equal to %s.',
        'msgNoData'                => 'No items found.',
        'msgCreatedSuccess'        => '%s is created successfully.',
        'msgUpdatedSuccess'        => '%s is updated successfully.',
        'msgManyUpdatedSuccess'    => '%s are updated successfully.',
        'msgDeletedSuccess'        => '%s is deleted successfully.',
        'msgCannotCreated'         => '%s cannot be created.',
        'msgCannotEdited'          => '%s cannot be edited.',
        'msgCannotDeleted'         => '%s cannot be deleted.',
        'msgWrongDeliveryAddress'  => 'Please check Postal Code and Delivery Address. It should be matched together!',
        'msgTokenExpired'          => 'Token has expired',
        'msgUpdateSuccessShort'    => 'Successfully updated.',
        'msgCannotEditedShort'     => 'Updated Failed.',
        'msgItemExist'             => '%s already existed.'
    );

    /**
     * Generate message content
     * @example MsgUtils::generateMessage('msgGreaterThan', 'Date Start', 'Date End')
     * @return string message content
     */
    public static function generate()
    {
        $messageContent = "";
        $args = func_get_args();
        $msgId = array_shift($args);
        if (isset(self::$messageContent[$msgId])) {
            $messageContent = count($args) > 0
                                ? vsprintf(self::$messageContent[$msgId], $args)
                                : self::$messageContent[$msgId];
        }
        return $messageContent;
    }
}
