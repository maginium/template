<?php

declare(strict_types=1);

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Setup\Fixtures\ImagesGenerator;

use Exception;
use Magento\Catalog\Model\Product\Media\Config;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;

/**
 * Create image with passed config and put it to media tmp folder.
 */
class ImagesGenerator
{
    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var Config
     */
    private $mediaConfig;

    /**
     * @param Filesystem $filesystem
     * @param Config $mediaConfig
     */
    public function __construct(
        Filesystem $filesystem,
        Config $mediaConfig,
    ) {
        $this->filesystem = $filesystem;
        $this->mediaConfig = $mediaConfig;
    }

    /**
     * Generates image from $data and puts its to /tmp folder.
     *
     * @param array $config
     *
     * @throws Exception
     *
     * @return string $imagePath
     */
    public function generate($config)
    {
        // phpcs:disable Magento2.Functions.DiscouragedFunction
        $binaryData = '';
        $data = mb_str_split(sha1($config['image-name']), 2);

        foreach ($data as $item) {
            $binaryData .= base_convert($item, 16, 2);
        }
        $binaryData = mb_str_split($binaryData, 1);

        $image = imagecreate($config['image-width'], $config['image-height']);
        $bgColor = imagecolorallocate($image, 240, 240, 240);
        // mt_rand() here is not for cryptographic use.
        // phpcs:ignore Magento2.Security.InsecureFunction
        $fgColor = imagecolorallocate($image, mt_rand(0, 230), mt_rand(0, 230), mt_rand(0, 230));
        $colors = [$fgColor, $bgColor];
        imagefilledrectangle($image, 0, 0, $config['image-width'], $config['image-height'], $bgColor);

        for ($row = 10; $row < ($config['image-height'] - 10); $row += 10) {
            for ($col = 10; $col < ($config['image-width'] - 10); $col += 10) {
                if (next($binaryData) === false) {
                    reset($binaryData);
                }

                imagefilledrectangle($image, $row, $col, $row + 10, $col + 10, $colors[current($binaryData)]);
            }
        }

        $mediaDirectory = $this->filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $relativePathToMedia = $mediaDirectory->getRelativePath($this->mediaConfig->getBaseTmpMediaPath());
        $mediaDirectory->create($relativePathToMedia);

        $imagePath = $relativePathToMedia . DIRECTORY_SEPARATOR . $config['image-name'];
        $imagePath = preg_replace('|/{2,}|', '/', $imagePath);
        $memory = fopen('php://memory', 'r+');

        if (! imagejpeg($image, $memory)) {
            throw new Exception('Could not create picture ' . $imagePath);
        }
        $mediaDirectory->writeFile($imagePath, stream_get_contents($memory, -1, 0));
        fclose($memory);
        imagedestroy($image);
        // phpcs:enable

        return $imagePath;
    }
}
