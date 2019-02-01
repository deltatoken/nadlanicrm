<?php
/************************************************************************
 * This file is part of NadlaniCrm.
 *
 * NadlaniCrm - Open Source CRM application.
 * Copyright (C) 2014-2018 Pablo Rotem
 * Website: https://www.facebook.com/sites4u2
 *
 * NadlaniCrm is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * NadlaniCrm is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with NadlaniCrm. If not, see http://www.gnu.org/licenses/.
 *
 * The interactive user interfaces in modified source and object code versions
 * of this program must display Appropriate Legal Notices, as required under
 * Section 5 of the GNU General Public License version 3.
 *
 * In accordance with Section 7(b) of the GNU General Public License version 3,
 * these Appropriate Legal Notices must retain the display of the "NadlaniCrm" word.
 ************************************************************************/

namespace Nadlani\Core\Utils;

class Route
{
    protected $data = null;

    private $fileManager;
    private $config;
    private $metadata;

    protected $cacheFile = 'data/cache/application/routes.php';

    protected $paths = array(
        'corePath' => 'application/Nadlani/Resources/routes.json',
        'modulePath' => 'application/Nadlani/Modules/{*}/Resources/routes.json',
        'customPath' => 'custom/Nadlani/Custom/Resources/routes.json',
    );

    public function __construct(Config $config, Metadata $metadata, File\Manager $fileManager)
    {
        $this->config = $config;
        $this->metadata = $metadata;
        $this->fileManager = $fileManager;
    }

    protected function getConfig()
    {
        return $this->config;
    }

    protected function getFileManager()
    {
        return $this->fileManager;
    }

    protected function getMetadata()
    {
        return $this->metadata;
    }

    public function get($key = '', $returns = null)
    {
        if (!isset($this->data)) {
            $this->init();
        }

        if (empty($key)) {
            return $this->data;
        }

        $keys = explode('.', $key);

        $lastRoute = $this->data;
        foreach($keys as $keyName) {
            if (isset($lastRoute[$keyName]) && is_array($lastRoute)) {
                $lastRoute = $lastRoute[$keyName];
            } else {
                return $returns;
            }
        }

        return $lastRoute;
    }

    public function getAll()
    {
        return $this->get();
    }

    protected function init()
    {
        if (file_exists($this->cacheFile) && $this->getConfig()->get('useCache')) {
            $this->data = $this->getFileManager()->getPhpContents($this->cacheFile);
        } else {
            $this->data = $this->unify();

            if ($this->getConfig()->get('useCache')) {
                $result = $this->getFileManager()->putPhpContents($this->cacheFile, $this->data);
                if ($result == false) {
                    throw new \Nadlani\Core\Exceptions\Error('Route - Cannot save unified routes');
                }
            }
        }
    }

    /**
     * Unify routes
     *
     * @return array
     */
    protected function unify()
    {
        // for custom
        $data = $this->getAddData([], $this->paths['customPath']);

        // for module
        $moduleData = [];
        foreach ($this->getMetadata()->getModuleList() as $moduleName) {
            $modulePath = str_replace('{*}', $moduleName, $this->paths['modulePath']);
            foreach ($this->getAddData([], $modulePath) as $row) {
                $moduleData[$row['method'].$row['route']] = $row;
            }
        }
        $data = array_merge($data, array_values($moduleData));

        // for core
        $data = $this->getAddData($data, $this->paths['corePath']);

        return $data;
    }

    protected function getAddData($currData, $routeFile)
    {
        if (file_exists($routeFile)) {
            $content = $this->getFileManager()->getContents($routeFile);
            $arrayContent = Json::getArrayData($content);
            if (empty($arrayContent)) {
                $GLOBALS['log']->error('Route::unify() - Empty file or syntax error - ['.$routeFile.']');
                return $currData;
            }

            $currData = $this->addToData($currData, $arrayContent);
        }

        return $currData;
    }

    protected function addToData($data, $newData)
    {
        if (!is_array($newData)) {
            return $data;
        }

        foreach ($newData as $route) {
            $route['route'] = $this->adjustPath($route['route']);
            $data[] = $route;
        }

        return $data;
    }

    /**
     * Check and adjust the route path
     *
     * @param string $routePath - it can be "/App/user",  "App/user"
     *
     * @return string - "/App/user"
     */
    protected function adjustPath($routePath)
    {
        $routePath = trim($routePath);

        if (substr($routePath,0,1) != '/') {
            return '/'.$routePath;
        }

        return $routePath;
    }
}