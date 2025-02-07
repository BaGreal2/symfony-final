<?php

namespace App\Service;

use App\Entity\Setting;
use Doctrine\ORM\EntityManagerInterface;

class SettingService
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Retrieve the value for a given setting name.
     *
     * @param string $name
     * @param mixed $default Value to return if the setting is not found
     * @return mixed
     */
    public function getSettingValue(string $name, $default = null)
    {
        $setting = $this->entityManager
            ->getRepository(Setting::class)
            ->findOneBy(['name' => $name]);

        return $setting ? $setting->getValue() : $default;
    }

    /**
     * Create or update a setting.
     *
     * @param string $name
     * @param mixed $value
     */
    public function setSettingValue(string $name, $value): void
    {
        $repository = $this->entityManager->getRepository(Setting::class);
        $setting = $repository->findOneBy(['name' => $name]);

        if (!$setting) {
            $setting = new Setting();
            $setting->setName($name);
        }

        $setting->setValue($value);
        $this->entityManager->persist($setting);
        $this->entityManager->flush();
    }
}
