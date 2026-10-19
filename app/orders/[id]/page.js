"use client";

import { useEffect, useState } from "react";
import { useParams } from "next/navigation";
import Link from "next/link";
import { apiFetch } from "@/lib/api";

export default function OrderDetailPage() {
  const { id } = useParams();
  const [order, setOrder] = useState(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState("");

  useEffect(() => {
    apiFetch(`/orders/${id}`)
      .then((data) => setOrder(data))
      .catch((err) => setError(err.message))
      .finally(() => setLoading(false));
  }, [id]);

  if (loading) return <p style={{ padding: 20 }}>Loading...</p>;
  if (error) return <p style={{ padding: 20, color: "red" }}>{error}</p>;
  if (!order) return <p style={{ padding: 20 }}>Order tidak ditemukan</p>;

  return (
    <div style={{ padding: 20, maxWidth: 600 }}>
      <Link href="/">&larr; Kembali ke Home</Link>
      <h1>Pesanan Berhasil Dibuat! 🎉</h1>
      <p>Nomor Order: <strong>{order.order_number}</strong></p>
      <p>Status: {order.status}</p>
      <p>Total: Rp {Number(order.total_amount).toLocaleString("id-ID")}</p>
      <p>Alamat: {order.shipping_address}</p>

      <h3>Item Pesanan:</h3>
      {order.items.map((item) => (
        <div key={item.id} style={{ borderBottom: "1px solid #eee", padding: 8 }}>
          <p>{item.product.name} x {item.quantity}</p>
          <p>Rp {Number(item.price * item.quantity).toLocaleString("id-ID")}</p>
        </div>
      ))}

      <h3>Pembayaran:</h3>
      <p>Metode: {order.payment?.method}</p>
      <p>Status: {order.payment?.status}</p>
    </div>
  );
}