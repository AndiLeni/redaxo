<?php

/**
 * Klasse zum pr�fen ob Plugins installiert/aktiviert sind
 * @package redaxo4
 * @version svn:$Id$
 */

class OOPlugin extends rex_addon
{
  /**
   * Erstellt eine OOPlugin instanz
   * 
   * @param string $addon Name des Addons
   */
  public function __construct($addon, $plugin)
  {
    parent::__construct(array($addon, $plugin));
  }
  
  /**
   * @override
   * @see redaxo/include/classes/rex_addon#isAvailable($addon)
   */
  public function isAvailable($addon, $plugin)
  {
    return parent::isAvailable(array($addon, $plugin));
  }

  /**
   * @override
   * @see redaxo/include/classes/rex_addon#isActivated($addon)
   */
  public function isActivated($addon, $plugin)
  {
    return parent::isActivated(array($addon, $plugin));
  }

  /**
   * @override
   * @see redaxo/include/classes/rex_addon#isInstalled($addon)
   */
  public function isInstalled($addon, $plugin)
  {
    return parent::isInstalled(array($addon, $plugin));
  }

  /**
   * @override
   * @see redaxo/include/classes/rex_addon#getSupportPage($addon, $default)
   */
  public function getSupportPage($addon, $plugin, $default = null)
  {
    return parent::getSupportPage(array($addon, $plugin), $default);
  }
  
  /**
   * @override
   * @see redaxo/include/classes/rex_addon#getVersion($addon, $default)
   */
  public function getVersion($addon, $plugin, $default = null)
  {
    return parent::getVersion(array($addon, $plugin), $default);
  }
  
  /**
   * @override
   * @see redaxo/include/classes/rex_addon#getAuthor($addon, $default)
   */
  public function getAuthor($addon, $plugin, $default = null)
  {
    return parent::getAuthor(array($addon, $plugin), $default);
  }
  
  /**
   * @override
   * @see redaxo/include/classes/rex_addon#getProperty($addon, $property, $default)
   */
  public function getProperty($addon, $plugin, $property, $default = null)
  {
    return parent::getProperty(array($addon, $plugin), $property, $default);
  }
  
  /**
   * @override
   * @see redaxo/include/classes/rex_addon#setProperty($addon, $property, $value)
   */
  public function setProperty($addon, $plugin, $property, $value)
  {
    return parent::setProperty(array($addon, $plugin), $property, $value);
  }
  
  /**
   * Gibt ein Array aller verf�gbaren Plugins zur�ck f�r das �bergebene Addon zur�ck.
   * 
   * @param string $addon Name des Addons
   * 
   * @return array Array aller verf�gbaren Plugins
   */
  static public function getAvailablePlugins($addon)
  {
    $avail = array();
    foreach(OOPlugin::getRegisteredPlugins($addon) as $plugin)
    {
      if(OOPlugin::isAvailable($addon, $plugin))
      {
        $avail[] = $plugin;
      }
    }

    return $avail;
  }
  

  /**
   * Gibt ein Array aller installierten Plugins zur�ck f�r das �bergebene Addon zur�ck.
   * 
   * @param string $addon Name des Addons
   * 
   * @return array Array aller registrierten Plugins
   */
  static public function getInstalledPlugins($addon)
  {
    $avail = array();
    foreach(OOPlugin::getRegisteredPlugins($addon) as $plugin)
    {
      if(OOPlugin::isInstalled($addon, $plugin))
      {
        $avail[] = $plugin;
      }
    }

    return $avail;
  }

  /**
   * Gibt ein Array aller registrierten Plugins zur�ck f�r das �bergebene Addon zur�ck.
   * Ein Plugin ist registriert, wenn es dem System bekannt ist (plugins.inc.php).
   * 
   * @param string $addon Name des Addons
   * 
   * @return array Array aller registrierten Plugins
   */
  static public function getRegisteredPlugins($addon)
  {
    global $REX;

    $plugins = array();
    if(isset($REX['ADDON']) && is_array($REX['ADDON']) &&
       isset($REX['ADDON']['plugins']) && is_array($REX['ADDON']['plugins']) &&
       isset($REX['ADDON']['plugins'][$addon]) && is_array($REX['ADDON']['plugins'][$addon]) &&
       isset($REX['ADDON']['plugins'][$addon]['install']) && is_array($REX['ADDON']['plugins'][$addon]['install']))
    {
      $plugins = array_keys($REX['ADDON']['plugins'][$addon]['install']);
    }
    
    return $plugins;
  }
}
