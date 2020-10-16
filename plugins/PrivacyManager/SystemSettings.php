<?php
/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 *
 */
namespace Piwik\Plugins\PrivacyManager;

use Piwik\Container\StaticContainer;
use Piwik\Log;
use Piwik\Piwik;
use Piwik\Plugin\Manager;
use Piwik\Settings\Plugin\SystemSetting;
use Piwik\Settings\FieldConfig;

/**
 * Defines Settings for PrivacyManager.
 */
class SystemSettings extends \Piwik\Settings\Plugin\SystemSettings
{
    /** @var SystemSetting */
    public $privacyPolicyUrl;

    /** @var SystemSetting */
    public $termsAndConditionUrl;

    /** @var SystemSetting */
    public $showInEmbeddedWidgets;

    /** @var SystemSetting */
    public $forceCookielessTracking;

    protected function init()
    {
        $this->privacyPolicyUrl = $this->createPrivacyPolicyUrlSetting();
        $this->termsAndConditionUrl = $this->createTermsAndConditionUrlSetting();
        $this->showInEmbeddedWidgets = $this->createShowInEmbeddedWidgetsSetting();
        $this->forceCookielessTracking = $this->createForceCookielessTrackingSetting();
    }

    private function createPrivacyPolicyUrlSetting(): SystemSetting
    {
        return $this->makeSetting('privacyPolicyUrl', $default = '', FieldConfig::TYPE_STRING, function (FieldConfig $field) {
            $field->title = Piwik::translate('PrivacyManager_PrivacyPolicyUrl');
            $field->uiControl = FieldConfig::UI_CONTROL_TEXT;
            $field->description = Piwik::translate('PrivacyManager_PrivacyPolicyUrlDescription') . ' ' .
                Piwik::translate('PrivacyManager_PrivacyPolicyUrlDescriptionSuffix', ['anonymous']);
        });
    }

    private function createTermsAndConditionUrlSetting(): SystemSetting
    {
        return $this->makeSetting('termsAndConditionUrl', $default = '', FieldConfig::TYPE_STRING, function (FieldConfig $field) {
            $field->title = Piwik::translate('PrivacyManager_TermsAndConditionUrl');
            $field->uiControl = FieldConfig::UI_CONTROL_TEXT;
            $field->description = Piwik::translate('PrivacyManager_TermsAndConditionUrlDescription') . ' ' .
                Piwik::translate('PrivacyManager_PrivacyPolicyUrlDescriptionSuffix', ['anonymous']);
        });
    }

    private function createShowInEmbeddedWidgetsSetting(): SystemSetting
    {
        return $this->makeSetting('showInEmbeddedWidgets', $default = false, FieldConfig::TYPE_BOOL, function (FieldConfig $field) {
            $field->title = Piwik::translate('PrivacyManager_ShowInEmbeddedWidgets');
            $field->uiControl = FieldConfig::UI_CONTROL_CHECKBOX;
            $field->description = Piwik::translate('PrivacyManager_ShowInEmbeddedWidgetsDescription');
        });
    }

    private function createForceCookielessTrackingSetting(): SystemSetting
    {
        return $this->makeSetting('forceCookielessTracking', $default = false, FieldConfig::TYPE_BOOL, function (FieldConfig $field) {
            $field->title = Piwik::translate('PrivacyManager_ForceCookielessTracking');
            $field->uiControl = FieldConfig::UI_CONTROL_CHECKBOX;
            $field->description = Piwik::translate('PrivacyManager_ForceCookielessTrackingDescription');
        });
    }

    public function save()
    {
        parent::save();

        try {
            if (Manager::getInstance()->isPluginActivated('CustomJsTracker')) {
                $trackerUpdater = StaticContainer::get('Piwik\Plugins\CustomJsTracker\TrackerUpdater');
                $trackerUpdater->update();
            }
        } catch (\Exception $e) {
            Log::error('There was an error while updating the javascript tracker: ' . $e->getMessage());
        }
    }
}
