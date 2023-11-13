<?php

$mapping = array(
    'Aws\S3\StreamWrapper' => __DIR__ . '/Aws/S3/StreamWrapper.php',
    'Aws\S3\BucketEndpointArnMiddleware' => __DIR__ . '/Aws/S3/BucketEndpointArnMiddleware.php',
    'Aws\S3\ObjectUploader' => __DIR__ . '/Aws/S3/ObjectUploader.php',
    'Aws\S3\ValidateResponseChecksumParser' => __DIR__ . '/Aws/S3/ValidateResponseChecksumParser.php',
    'Aws\S3\ApplyChecksumMiddleware' => __DIR__ . '/Aws/S3/ApplyChecksumMiddleware.php',
    'Aws\S3\UseArnRegion\Configuration' => __DIR__ . '/Aws/S3/UseArnRegion/Configuration.php',
    'Aws\S3\UseArnRegion\Exception\ConfigurationException' => __DIR__ . '/Aws/S3/UseArnRegion/Exception/ConfigurationException.php',
    'Aws\S3\UseArnRegion\ConfigurationInterface' => __DIR__ . '/Aws/S3/UseArnRegion/ConfigurationInterface.php',
    'Aws\S3\UseArnRegion\ConfigurationProvider' => __DIR__ . '/Aws/S3/UseArnRegion/ConfigurationProvider.php',
    'Aws\S3\RegionalEndpoint\ConfigurationInterface' => __DIR__ . '/Aws/S3/RegionalEndpoint/ConfigurationInterface.php',
    'Aws\S3\RegionalEndpoint\Exception\ConfigurationException' => __DIR__ . '/Aws/S3/RegionalEndpoint/Exception/ConfigurationException.php',
    'Aws\S3\RegionalEndpoint\ConfigurationProvider' => __DIR__ . '/Aws/S3/RegionalEndpoint/ConfigurationProvider.php',
    'Aws\S3\RegionalEndpoint\Configuration' => __DIR__ . '/Aws/S3/RegionalEndpoint/Configuration.php',
    'Aws\S3\Crypto\S3EncryptionClient' => __DIR__ . '/Aws/S3/Crypto/S3EncryptionClient.php',
    'Aws\S3\Crypto\HeadersMetadataStrategy' => __DIR__ . '/Aws/S3/Crypto/HeadersMetadataStrategy.php',
    'Aws\S3\Crypto\S3EncryptionMultipartUploaderV2' => __DIR__ . '/Aws/S3/Crypto/S3EncryptionMultipartUploaderV2.php',
    'Aws\S3\Crypto\UserAgentTrait' => __DIR__ . '/Aws/S3/Crypto/UserAgentTrait.php',
    'Aws\S3\Crypto\S3EncryptionMultipartUploader' => __DIR__ . '/Aws/S3/Crypto/S3EncryptionMultipartUploader.php',
    'Aws\S3\Crypto\S3EncryptionClientV2' => __DIR__ . '/Aws/S3/Crypto/S3EncryptionClientV2.php',
    'Aws\S3\Crypto\CryptoParamsTrait' => __DIR__ . '/Aws/S3/Crypto/CryptoParamsTrait.php',
    'Aws\S3\Crypto\CryptoParamsTraitV2' => __DIR__ . '/Aws/S3/Crypto/CryptoParamsTraitV2.php',
    'Aws\S3\Crypto\InstructionFileMetadataStrategy' => __DIR__ . '/Aws/S3/Crypto/InstructionFileMetadataStrategy.php',
    'Aws\S3\S3Client' => __DIR__ . '/Aws/S3/S3Client.php',
    'Aws\S3\MultipartUploader' => __DIR__ . '/Aws/S3/MultipartUploader.php',
    'Aws\S3\PermanentRedirectMiddleware' => __DIR__ . '/Aws/S3/PermanentRedirectMiddleware.php',
    'Aws\S3\S3UriParser' => __DIR__ . '/Aws/S3/S3UriParser.php',
    'Aws\S3\BatchDelete' => __DIR__ . '/Aws/S3/BatchDelete.php',
    'Aws\S3\RetryableMalformedResponseParser' => __DIR__ . '/Aws/S3/RetryableMalformedResponseParser.php',
    'Aws\S3\S3EndpointMiddleware' => __DIR__ . '/Aws/S3/S3EndpointMiddleware.php',
    'Aws\S3\AmbiguousSuccessParser' => __DIR__ . '/Aws/S3/AmbiguousSuccessParser.php',
    'Aws\S3\Exception\S3MultipartUploadException' => __DIR__ . '/Aws/S3/Exception/S3MultipartUploadException.php',
    'Aws\S3\Exception\PermanentRedirectException' => __DIR__ . '/Aws/S3/Exception/PermanentRedirectException.php',
    'Aws\S3\Exception\S3Exception' => __DIR__ . '/Aws/S3/Exception/S3Exception.php',
    'Aws\S3\Exception\DeleteMultipleObjectsException' => __DIR__ . '/Aws/S3/Exception/DeleteMultipleObjectsException.php',
    'Aws\S3\GetBucketLocationParser' => __DIR__ . '/Aws/S3/GetBucketLocationParser.php',
    'Aws\S3\S3ClientTrait' => __DIR__ . '/Aws/S3/S3ClientTrait.php',
    'Aws\S3\MultipartCopy' => __DIR__ . '/Aws/S3/MultipartCopy.php',
    'Aws\S3\MultipartUploadingTrait' => __DIR__ . '/Aws/S3/MultipartUploadingTrait.php',
    'Aws\S3\PutObjectUrlMiddleware' => __DIR__ . '/Aws/S3/PutObjectUrlMiddleware.php',
    'Aws\S3\EndpointRegionHelperTrait' => __DIR__ . '/Aws/S3/EndpointRegionHelperTrait.php',
    'Aws\S3\CalculatesChecksumTrait' => __DIR__ . '/Aws/S3/CalculatesChecksumTrait.php',
    'Aws\S3\SSECMiddleware' => __DIR__ . '/Aws/S3/SSECMiddleware.php',
    'Aws\S3\S3ClientInterface' => __DIR__ . '/Aws/S3/S3ClientInterface.php',
    'Aws\S3\Transfer' => __DIR__ . '/Aws/S3/Transfer.php',
    'Aws\S3\PostObject' => __DIR__ . '/Aws/S3/PostObject.php',
    'Aws\S3\ObjectCopier' => __DIR__ . '/Aws/S3/ObjectCopier.php',
    'Aws\S3\S3MultiRegionClient' => __DIR__ . '/Aws/S3/S3MultiRegionClient.php',
    'Aws\S3\BucketEndpointMiddleware' => __DIR__ . '/Aws/S3/BucketEndpointMiddleware.php',
    'Aws\S3\PostObjectV4' => __DIR__ . '/Aws/S3/PostObjectV4.php'
);

spl_autoload_register(function ($class) use ($mapping) {
    if (isset($mapping[$class])) {
        require $mapping[$class];
    }
}, true);