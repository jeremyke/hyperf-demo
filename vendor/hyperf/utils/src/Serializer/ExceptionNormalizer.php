<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://doc.hyperf.io
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf-cloud/hyperf/blob/master/LICENSE
 */

namespace Hyperf\Utils\Serializer;

use Hyperf\Di\ReflectionManager;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ExceptionNormalizer implements NormalizerInterface, DenormalizerInterface, CacheableSupportsMethodInterface
{
    /**
     * {@inheritdoc}
     */
    public function denormalize($data, $class, $format = null, array $context = [])
    {
        if (is_string($data)) {
            $ex = unserialize($data);
            if ($ex instanceof \Exception) {
                return $ex;
            }

            // Retry handle it if the exception not instanceof \Exception.
            $data = $ex;
        }
        if (is_array($data) && isset($data['message'], $data['code'])) {
            try {
                $exception = new $class($data['message'], $data['code']);
                foreach (['file', 'line'] as $attribute) {
                    if (isset($data[$attribute])) {
                        $property = ReflectionManager::reflectProperty($class, $attribute);
                        $property->setAccessible(true);
                        $property->setValue($exception, $data[$attribute]);
                    }
                }
                return $exception;
            } catch (\ReflectionException $e) {
                return new \RuntimeException(sprintf(
                    'Bad data %s: %s',
                    $data['class'],
                    $data['message']
                ), $data['code']);
            } catch (\TypeError $e) {
                return new \RuntimeException(sprintf(
                    'Uncaught data %s: %s',
                    $data['class'],
                    $data['message']
                ), $data['code']);
            }
        }

        return new \RuntimeException('Bad data data: ' . json_encode($data));
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization($data, $type, $format = null)
    {
        return class_exists($type) && is_a($type, \Exception::class, true);
    }

    /**
     * {@inheritdoc}
     */
    public function normalize($object, $format = null, array $context = [])
    {
        if ($object instanceof \Serializable) {
            return serialize($object);
        }
        /* @var \Exception $object */
        return [
            'message' => $object->getMessage(),
            'code' => $object->getCode(),
            'file' => $object->getFile(),
            'line' => $object->getLine(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof \Exception;
    }

    /**
     * {@inheritdoc}
     */
    public function hasCacheableSupportsMethod(): bool
    {
        return \get_class($this) === __CLASS__;
    }
}
