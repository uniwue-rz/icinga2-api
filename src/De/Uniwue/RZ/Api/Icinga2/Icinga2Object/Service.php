<?php
/**
 * Created by PhpStorm.
 * User: poa32kc
 * Date: 14.12.17
 * Time: 15:16
 */

namespace De\Uniwue\RZ\Api\Icinga2\Icinga2Object;


use Httpful\Response;

class Service extends Icinga2Object implements Icinga2ObjectInterface
{

    public function getChildren()
    {
    }

    /**
     * This is useful if the service has a passive CheckCommand, i.e.
     * when the service definition contains enable_active_checks = false.
     *
     * See
     *   -
     * https://icinga.com/docs/icinga2/latest/doc/12-icinga2-api/#process-check-result
     *   - https://itgix.com/blog/post/icinga2-api-and-passive-checks/
     *
     * @param int $exit_status
     *   0=OK, 1=WARNING, 2=CRITICAL, 3=UNKNOWN
     * @param string $plugin_output
     * @param string[] $performance_data
     *   Structure of the individual strings; sections are separated by ;
     *   1. Section: Everything before the '=' is the name of the value.
     *     For example 'disk_space_percent'
     *     The first field after the '=' is meant to be the current state of
     *     the
     *     service always followed by ';'. That's the least amount of
     *     information required to receive performance data which can be
     *     interpreted by the monitoring. Additionally, you can add the unit to
     *     make sure the monitoring understands what the value represents.
     *     Examples are MB, ms or %
     *
     *   2. Section: warning level
     *   3. Section: critical level
     *
     *   4. Section: theoretical minimum of the scale, usually 0
     *   5. Section: maximum amount possible for this performance number.
     *
     * @return \Httpful\Response
     * @throws ConnectionErrorException when unable to parse or communicate w server
     *
     */
    public function processCheckResult(int $exit_status, string $plugin_output, array $performance_data = []): Response
    {
        $request = $this->icinga2->buildRequest('v1/actions/process-check-result');
        $attrs = $this->getAttributes();
        $request->payload = json_encode([
            'type' => 'Service',
            'service' => $this->getName(),
            'exit_status' => $exit_status,
            'plugin_output' => $plugin_output,
            'performance_data' => $performance_data,
        ]);
        return $request->send();
    }

}