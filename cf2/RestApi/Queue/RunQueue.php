<?php


namespace calderawp\calderaforms\cf2\RestApi\Queue;


use calderawp\calderaforms\cf2\RestApi\AuthorizesRestApiRequestWithCfProKeys;
use calderawp\calderaforms\cf2\RestApi\Endpoint;
use calderawp\calderaforms\cf2\Services\QueueSchedulerService;


class RunQueue extends Endpoint
{
	use AuthorizesRestApiRequestWithCfProKeys;

	public function getUri()
	{
		return 'queue';
	}

	/** @inheritdoc */
    protected function getArgs()
    {
        return [

            'methods' => 'POST',
            'callback' => [$this, 'runQueue'],
            'permission_callback' => [$this, 'checkKeys' ],
            'args' => [
                'jobs' => [
                    'description' => __('Total jobs to run per back', 'caldera-forms'),
					'required' => false,
					'default' => 10,
					'sanitize_callback' => 'absint'

				],
				'public' => [
					'type' => 'string',
					'required' => false,
					'default' => ''
				]
            ]
        ];
    }


    /**
     * Trigger queue manger from remote ping
     *
     * @since 1.8.0
     *
     * @param \WP_REST_Request $request
     * @return \WP_REST_Response
     */

    /**
     * @param \WP_REST_Request $request
     * @return mixed|null|\WP_REST_Response
     */
    public function runQueue(\WP_REST_Request $request)
    {
    	$totalJobs = caldera_forms_get_v2_container()
		   ->getService(QueueSchedulerService::class )
		   ->runJobs($request['jobs']);

    	$statusCode = $totalJobs > 0 ? 201 : 200;
        $response =  rest_ensure_response(['totalJobs' => $totalJobs]);
		$response->set_status($statusCode);
        return $response;
    }



}
