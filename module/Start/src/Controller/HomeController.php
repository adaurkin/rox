<?php

namespace Rox\Start\Controller;

use Rox\CommunityNews\Model\CommunityNews;
use Rox\Core\Controller\AbstractController;
use Rox\Main\Home\HomeModel as HomeService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class HomeController extends AbstractController
{
    /**
     * @var HomeService
     */
    protected $homeService;

    public function __construct()
    {
        $this->homeService = new HomeService();
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function showMessagesAction(Request $request)
    {
        $all = $request->query->get('all');
        $unread = $request->query->get('unread');

        $member = $this->getMember();

        $messages = $this->homeService->getMessages($member, $all, $unread, 4);

        $content = $this->render('@start/widget/messages.html.twig', [
            'messages' => $messages,
        ]);

        return new Response($content);
    }

    public function showNotificationsAction()
    {
        $member = $this->getMember();
        $notifications = $this->homeService->getNotifications($member, 5);

        $content = $this->render('@start/widget/notifications.html.twig', [
            'notifications' => $notifications,
        ]);

        return new Response($content);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function showThreadsAction(Request $request)
    {
        $groups = $request->query->get('groups');
        $forum = $request->query->get('forum');
        $following = $request->query->get('following');

        $threads = $this->homeService->getThreads($this->getMember(), $groups, $forum, $following, 4);

        $content = $this->render('@start/widget/forums.html.twig', [
            'threads' => $threads,
        ]);

        return new Response($content);
    }

    public function showActivitiesAction()
    {
        $activities = $this->homeService->getActivities($this->getMember(), 4);

        $content = $this->render('@start/widget/activities.html.twig', [
            'activities' => $activities,
        ]);

        return new Response($content);
    }

    public function setAccommodationAction(Request $request)
    {
        $accommodation = $request->request->get('accommodation');

        switch ($accommodation) {
            case 'dontask':
            case 'dependonrequest':
            case 'anytime':
                $valid = true;
                break;
            default:
                $valid = false;
        }

        $member = $this->getMember();
        if ($valid) {
            $member->Accomodation = $accommodation;
            $member->save();
        }

        $profilePictureWithAccommodation = $this->render('@start/widget/profilepicturewithaccommodation.html.twig', [
            'member' => $member,
        ]);

        $accommodationHtml = $this->render('@start/widget/accommodation.html.twig', [
            'member' => $member,
        ]);

        return new JsonResponse([
            'profilePictureWithAccommodation' => $profilePictureWithAccommodation,
            'accommodationHtml' => $accommodationHtml,

        ]);
    }

    /**
     * Shows the home page.
     *
     * @return Response
     */
    public function showAction()
    {
        $donationCampaign = $this->homeService->getDonationCampaignDetails();
        $member = $this->getMember();
        $potentialGuests = $member->getPotentialGuests();
        $communityNews = new CommunityNews();
        $latestNews = $communityNews->getLatest();

        $content = $this->render(
            '@start/home.html.twig',
            [
                'campaign' => $donationCampaign,
                'travellers' => $potentialGuests,
                'communityNews' => $latestNews,
            ]
        );

        return new Response($content);
    }
}
