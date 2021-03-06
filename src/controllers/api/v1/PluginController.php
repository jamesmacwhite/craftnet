<?php

namespace craftnet\controllers\api\v1;

use craftnet\ChangelogParser;
use craftnet\controllers\api\BaseApiController;
use craftnet\Module;
use craftnet\plugins\Plugin;
use yii\web\Response;

/**
 * Class PluginController
 */
class PluginController extends BaseApiController
{
    // Public Methods
    // =========================================================================

    /**
     * Handles /v1/plugin/<pluginId> requests.
     *
     * @return Response
     */
    public function actionIndex($pluginId): Response
    {
        $plugin = Plugin::find()
            ->id($pluginId)
            ->anyStatus()
            ->withLatestReleaseInfo(true, $this->cmsVersion)
            ->one();

        if (!$plugin) {
            return $this->asErrorJson("Couldn't find plugin");
        }

        return $this->asJson($this->transformPlugin($plugin, true));
    }

    /**
     * Handles /v1/plugin/<pluginId>/changelog requests.
     *
     * @return Response
     */
    public function actionChangelog($pluginId): Response
    {
        $plugin = Plugin::find()
            ->id($pluginId)
            ->anyStatus()
            ->withLatestReleaseInfo()
            ->one();

        if (!$plugin) {
            return $this->asErrorJson("Couldn't find plugin");
        }

        $packageManager = Module::getInstance()->getPackageManager();
        $release = $packageManager->getRelease($plugin->packageName, $plugin->latestVersion);

        $releases = (new ChangelogParser())->parse($release->changelog ?? '');
        return $this->asJson(array_values($releases));
    }
}
