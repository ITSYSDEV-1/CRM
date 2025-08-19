<?php

namespace App\Helpers;

class UnitHelper
{
    /**
     * Get current unit configuration
     *
     * @return array
     */
    public static function getCurrentUnitConfig()
    {
        $currentUnit = env('UNIT', 'default');
        $unitsConfig = config('units');
        
        return $unitsConfig[$currentUnit] ?? $unitsConfig['default'];
    }
    
    /**
     * Get unit logo path
     *
     * @return string
     */
    public static function getUnitLogo()
    {
        $config = self::getCurrentUnitConfig();
        return asset($config['logo_path']);
    }
    
    /**
     * Get unit registration form background image
     *
     * @return string
     */
    public static function getRegistrationFormBackground()
    {
        $config = self::getCurrentUnitConfig();
        return asset($config['registration_form']['background_image']);
    }
    
    /**
     * Get unit registration form title
     *
     * @return string
     */
    public static function getRegistrationFormTitle()
    {
        $config = self::getCurrentUnitConfig();
        return $config['registration_form']['title'];
    }
    
    /**
     * Get unit registration form blade template
     *
     * @return string
     */
    public static function getRegistrationFormTemplate()
    {
        $config = self::getCurrentUnitConfig();
        return $config['registration_form']['blade_template'];
    }
    
    /**
     * Get unit name
     *
     * @return string
     */
    public static function getUnitName()
    {
        $config = self::getCurrentUnitConfig();
        return $config['name'];
    }
    
    /**
     * Get unit domain
     *
     * @return string
     */
    public static function getUnitDomain()
    {
        $config = self::getCurrentUnitConfig();
        return $config['domain'];
    }
    
    /**
     * Check if unit-specific template exists, fallback to default if not
     *
     * @return string
     */
    public static function getAvailableRegistrationTemplate()
    {
        $template = self::getRegistrationFormTemplate();
        
        // Check if the view exists
        if (view()->exists($template)) {
            return $template;
        }
        
        // Fallback to default template
        return 'reservation.registrationpdf';
    }
}