<?php

use App\Controller\LabelController;
use App\Entity\Label;
use App\Repository\LabelRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Serializer\SerializerInterface;

class LabelControllerTest extends TestCase
{
	public function testGetLabels(): void
	{
		$labelController = new LabelController();

		$labelRepository = $this->createMock(LabelRepository::class);
		$labelRepository->expects(self::once())->method('findAll')->willReturn([new Label(), new Label()]);
		$serializerInterface = $this->createMock(SerializerInterface::class);
		$serializerInterface->expects(self::once())->method('serialize')->willReturn('{"ok": "ok"}');

		$labels = $labelController->getLabels($labelRepository, $serializerInterface);

		$this->assertSame($labels->getStatusCode(), 200);
		$this->assertSame($labels->getContent(), '{"ok": "ok"}');
	}

	public function testGetLabelsEmpty(): void
	{
		$labelController = new LabelController();

		$labelRepository = $this->createMock(LabelRepository::class);
		$labelRepository->expects(self::once())->method('findAll')->willReturn(null);
		$serializerInterface = $this->createMock(SerializerInterface::class);
		$serializerInterface->expects(self::never())->method('serialize')->willReturn('{"ok": "ok"}');

		$this->expectException(NotFoundHttpException::class);

		$labelController->getLabels($labelRepository, $serializerInterface);
	}

	public function testGetLabel(): void
	{
		$labelController = new LabelController();

		$labelRepository = $this->createMock(LabelRepository::class);
		$labelRepository->expects(self::once())->method('find')->willReturn(new Label());
		$serializerInterface = $this->createMock(SerializerInterface::class);
		$serializerInterface->expects(self::once())->method('serialize')->willReturn('{"ok": "ok"}');

		$labels = $labelController->getLabel(1, $labelRepository, $serializerInterface,);

		$this->assertSame($labels->getStatusCode(), 200);
		$this->assertSame($labels->getContent(), '{"ok": "ok"}');
	}

	public function testGetLabelEmpty(): void
	{
		$labelController = new LabelController();

		$labelRepository = $this->createMock(LabelRepository::class);
		$labelRepository->expects(self::once())->method('find')->willReturn(null);
		$serializerInterface = $this->createMock(SerializerInterface::class);
		$serializerInterface->expects(self::never())->method('serialize')->willReturn('{"ok": "ok"}');

		$this->expectException(NotFoundHttpException::class);

		$labelController->getLabel(1, $labelRepository, $serializerInterface);
	}
}
