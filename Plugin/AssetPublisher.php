<?php

namespace Swissup\Rtl\Plugin;

use Swissup\Rtl\Model\MixinsRenderer;
use Swissup\Rtl\Model\MixinsRendererFactory;
use Magento\Framework\App\Filesystem\DirectoryList;

class AssetPublisher
{
    /**
     * @var MixinsRendererFactory
     */
    private $mixinsRendererFactory;

    /**
     * @var \Magento\Framework\Filesystem\Driver\File
     */
    private $fileDriver;

    /**
     * @var \Magento\Framework\Filesystem
     */
    private $filesystem;

    /**
     * @var \Magento\Framework\App\View\Asset\MaterializationStrategy\Copy
     */
    private $copyFile;

    /**
     * @var \Magento\Framework\Filesystem\Directory\WriteFactory
     */
    private $writeFactory;

    /**
     * @param MixinsRendererFactory $mixinsRendererFactory
     * @param \Magento\Framework\Filesystem\Driver\File $fileDriver
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Framework\App\View\Asset\MaterializationStrategy\Copy $copyFile
     * @param \Magento\Framework\Filesystem\Directory\WriteFactory $writeFactory
     */
    public function __construct(
        MixinsRendererFactory $mixinsRendererFactory,
        \Magento\Framework\Filesystem\Driver\File $fileDriver,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\App\View\Asset\MaterializationStrategy\Copy $copyFile,
        \Magento\Framework\Filesystem\Directory\WriteFactory $writeFactory
    ) {
        $this->mixinsRendererFactory = $mixinsRendererFactory;
        $this->fileDriver = $fileDriver;
        $this->filesystem = $filesystem;
        $this->copyFile = $copyFile;
        $this->writeFactory = $writeFactory;
    }

    /**
     * Grunt tasks compatibility. Inject modrtl mixins into '_modrtl.less' files.
     *
     * @param \Magento\Framework\App\View\Asset\Publisher $subject
     * @param bool $result
     * @param \Magento\Framework\View\Asset\LocalInterface $asset
     * @return bool
     */
    public function afterPublish(
        \Magento\Framework\App\View\Asset\Publisher $subject,
        $result,
        \Magento\Framework\View\Asset\LocalInterface $asset
    ) {
        if (!$result) {
            return $result;
        }

        if (strpos($asset->getFilePath(), MixinsRenderer::FILENAME) === false) {
            return $result;
        }

        try {
            $filepath = $asset->getSourceFile();
            $filename = basename($filepath);
        } catch (\Exception $e) {
            return $result;
        }

        $dirname = dirname($filepath);
        $assetPath = $asset->getPath();
        $contents = $this->getFileContents($filepath);
        $staticDir = $this->filesystem->getDirectoryWrite(DirectoryList::STATIC_VIEW);

        if ($contents &&
            strpos($contents, MixinsRenderer::PLACEHOLDER) !== false &&
            $staticDir->isExist($assetPath) // check if asset succesfully deployed
        ) {
            $contents = str_replace(
                MixinsRenderer::PLACEHOLDER,
                $this->mixinsRendererFactory->create()->render($asset->getContext()),
                $contents
            );

            $assetAbsolutePath = $staticDir->getAbsolutePath($assetPath);
            if (is_link($assetAbsolutePath)) {
                // delete link to empty _modrtl.less file
                $staticDir->delete($assetPath);

                // redeploy same file using copy strategy
                $this->copyFile->publishFile(
                    $this->writeFactory->create($dirname),
                    $staticDir,
                    $filename,
                    $assetPath
                );
            }

            // update content of deployed asset
            $this->fileDriver->filePutContents($assetAbsolutePath, $contents);
        }

        return $result;
    }

    /**
     * Get contents of the source file
     *
     * @param  string $filepath
     * @return string
     */
    private function getFileContents($filepath)
    {
        try {
            $contents = $this->fileDriver->fileGetContents($filepath);
        } catch (\Exception $e) {
            $contents = false;
        }
        return $contents;
    }
}
