<?php
namespace App\Controller;

use App\Service\ApiFormatter;
use App\Service\SettingService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SettingController extends AbstractController
{
    #[Route('/setting/test', name: 'setting_test')]
    public function test(SettingService $settingManager, ApiFormatter $apiFormatter): Response
    {
        $settingManager->setSettingValue('site_name', 'Test Site Name');

        $siteName = $settingManager->getSettingValue('site_name', 'Default Site Name');

        return $this->json($apiFormatter->format(['site_name' => $siteName]), 200);
    }
}


