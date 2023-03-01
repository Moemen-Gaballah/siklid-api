<?php

declare(strict_types=1);

namespace App\Siklid\Application\Box;

use App\Foundation\Action\AbstractAction;
use App\Foundation\Validation\ValidatorInterface;
use App\Siklid\Application\Box\Request\UpdateBoxRequest;
use App\Siklid\Application\Contract\Entity\BoxInterface;
use App\Siklid\Document\Box;
use App\Siklid\Security\UserResolverInterface;
use Doctrine\ODM\MongoDB\DocumentManager;

final class UpdateBox extends AbstractAction
{
    private UpdateBoxRequest $request;

    private DocumentManager $dm;

    private ValidatorInterface $validator;
    private UserResolverInterface $userResolver;

    public function __construct(
        UpdateBoxRequest $request,
        DocumentManager $dm,
        ValidatorInterface $validator,
        UserResolverInterface $userResolver,
        ?BoxInterface $box = null
    ) {
        $this->request = $request;
        $this->dm = $dm;
        $this->validator = $validator;
        $this->userResolver = $userResolver;
        $this->box = $box;
    }

    public function execute(): BoxInterface
    {
        $this->box->setUser($this->userResolver->getUser());

        if(isset($this->request->formInput()['name']))
        $this->box->setName($this->request->formInput()['name']);


        if(isset($this->request->formInput()['description']))
        $this->box->setDescription($this->request->formInput()['description']);
        $this->box->setHashtags(extract_hashtags((string)$this->box->getDescription()));

        $this->validator->validate($this->box);

        $this->dm->persist($this->box);
        $this->dm->flush();

        return $this->box;
    }

    public function setBox(BoxInterface $box): self
    {
        $this->box = $box;

        return $this;
    }
}
