<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\craft\salesforce\transformers;

use craft\helpers\Json;
use Psr\Http\Message\ResponseInterface;
use yii\base\DynamicModel;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class DynamicModelResponse
{
    use TransformResponseErrorsTrait;

    /**
     * @param ResponseInterface $response
     * @return DynamicModel
     */
    public function __invoke(ResponseInterface $response): DynamicModel
    {
        $data = Json::decodeIfJson(
            $response->getBody()->getContents()
        );

        if ($response->getStatusCode() === 200) {
            if (!$data) {
                $response->getBody()->rewind();

                var_dump($response);
                return new DynamicModel();
            }

            return new DynamicModel(array_keys($data), $data);
        }

        return $this->transformResponseErrors($response, $data);
    }
}
