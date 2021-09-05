<?php
namespace Danae\Faylin\App\Authorization;

use Psr\Http\Message\ServerRequestInterface as Request;

use Danae\Faylin\Model\User;


// Interface that defines an authorization strategy
interface AuthorizationStrategyInterface
{
  // Return if this strategy is able to authorize the request
  public function canAuthorize(Request $request): bool;

  // Return the authorized user from the request
  public function authorize(Request $request): array;
}
