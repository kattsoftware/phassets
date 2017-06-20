<?php

namespace Phassets\Deployers;

use Phassets\Asset;
use Phassets\Interfaces\CacheAdapter;
use Phassets\Interfaces\Configurator;
use Phassets\Interfaces\Deployer;

class AmazonS3Deployer implements Deployer
{
    const S3_URL_SCHEMA = 'https://s3.amazonaws.com/%s/%s';

    /**
     * @var Configurator
     */
    private $configurator;

    /**
     * @var CacheAdapter
     */
    private $cacheAdapter;

    /**
     * @var string AWS Access Key (see AWS Console)
     */
    private $awsAccessKey;

    /**
     * @var string AWS Secret Key (see AWS Console)
     */
    private $awsSecretKey;

    /**
     * @var string Bucket name
     */
    private $bucket;

    /**
     * Deployer constructor.
     *
     * @param Configurator $configurator Chosen and loaded Phassets configurator.
     * @param CacheAdapter $cacheAdapter Chosen and loaded Phassets cache adapter (if any)
     */
    public function __construct(Configurator $configurator, CacheAdapter $cacheAdapter)
    {
        $this->configurator = $configurator;
        $this->cacheAdapter = $cacheAdapter;
    }

    /**
     * Attempt to retrieve a previously deployed asset; if it does exist,
     * then return an absolute URL to its deployed version without performing
     * any further filters' actions.
     *
     * @param Asset $asset
     * @return string|bool An absolute URL to asset already-processed version or false
     *                     if the asset was never deployed using this class.
     */
    public function getDeployedFile(Asset $asset)
    {
        $computedOutput = $this->computeOutputBasename($asset);
        $awsS3Url = sprintf(self::S3_URL_SCHEMA, $this->bucket, $computedOutput);

        // TODO cache implementation

        $ch = curl_init($awsS3Url);

        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_exec($ch);
        $retcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        return $retcode == 200 ? $awsS3Url : false;
    }

    /**
     * Given an Asset instance, try to deploy the file using internal
     * rules of this deployer. Returns false in case of failure.
     *
     * @param Asset $asset
     * @return string|bool An absolute URL to asset already-processed version or false
     *                     if the asset wasn't deployed.
     */
    public function deploy(Asset $asset)
    {
        $computedOutput = $this->computeOutputBasename($asset);
        $awsS3Url = sprintf(self::S3_URL_SCHEMA, $this->bucket, $computedOutput);

        \S3::putObject(
            $asset->getContents(),
            $this->bucket,
            $computedOutput,
            \S3::ACL_PUBLIC_READ
        );

        // TODO check result?

        return $awsS3Url;
    }

    /**
     * This must return true/false if the current configuration allows
     * this deployer to deploy processed assets AND it can return previously
     * deployed assets as well.
     *
     * @return bool True if at this time Phassets can use this deployer to
     *              deploy and serve deployed assets, false otherwise.
     */
    public function isSupported()
    {
        if (!class_exists('\S3')) {
            return false;
        }

        $this->awsAccessKey = $this->configurator->getConfig('s3_deployer', 'aws_access_key');
        $this->awsSecretKey = $this->configurator->getConfig('s3_deployer', 'aws_secret_key');
        $this->bucket = $this->configurator->getConfig('s3_deployer', 'bucket');

        if (empty($this->awsAccessKey) || empty($this->awsSecretKey) ||  empty($this->bucket)) {
            return false;
        }

        \S3::setAuth($this->awsAccessKey, $this->awsSecretKey);

        return true;
    }

    /**
     * Generates the output full file name of an Asset instance.
     * Pattern: <original_file_name>_<last_modified_timestamp>[.<extension>]
     *
     * @param Asset $asset
     * @return string Generated basename of asset
     */
    private function computeOutputBasename(Asset $asset)
    {
        $ext = $asset->getExtension() ? '.' . $asset->getExtension() : '';

        return $asset->getFilename() . '_' . $asset->getModifiedTimestamp() . $ext;
    }
}
